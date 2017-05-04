#
# Portfolio Solver
#
# This script solves the following linear equation for portfolio optimization:
# ERtotal = SUM(ERi * Wi) for all assets in range(0, i)
#
# Docs / Example: https://docs.scipy.org/doc/scipy/reference/generated/scipy.optimize.linprog.html
#

#
# NOTE: SciPy's linprog function MINIMIZES all functions. Because our goal is to MAXIMIZE our formula, we have to solve the following:
# max(f(x)) == -min(-f(x))
# Source: http://stackoverflow.com/questions/30849883/linear-programming-with-scipy-optimize-linprog
#

# Imports
from scipy.optimize import linprog
import json
import sys

# Make sure a command line argument exists.
if len(sys.argv) != 2:
	print "Must include JSON object of portfolio."
	sys.exit()

data = None

# Make sure CLA is valid.
try:
	data = json.loads(sys.argv[1])
except Exception as e:
	print "Must include JSON object of portfolio."
	sys.exit()

# Portfolio
portfolio = [

	# Expected Return values.
	[ x["historicals"]["expected_return"] for x in data["tickers"] ],

	# Beta values.
	[ x["historicals"]["beta"] for x in data["tickers"] ],

	# Buckets for currencies.
	[ (1 if x["currency"] == "USD" else 0) for x in data["tickers"] ],

]

# Coefficients for linear equation.
c = [x * -1 for x in portfolio[0]]

# Coefficients for all constraints equations.
A = [

	# Portfolio weights must = 1.
	[1] * len(portfolio[0]),

	# Portfolio beta must equal given value.
	portfolio[1],

	# 70% of stocks must be from US stocks. 30% must be from INR stocks.
	[ 1 if x == 1 else 0 for x in portfolio[2] ]

]

# Equation result for all constraints equations.
b = [
	# Portfolio weight.
	1,

	# Stable beta value.
	data["portfolio"]["statistics"]["beta"],

	# Portfolio balance - USD vs INR
	0.7
]

# Set bounds for weights (must range from 0 to 1)
bounds = tuple([(0, 1) for x in [1] * len(portfolio[0])])

# Solve the linear function!
try:
	result = linprog(c, A_eq=A, b_eq=b, bounds=bounds, options={"disp": False}, method="simplex")
except Exception as e:
	print json.dumps({"message": str(e)})
	sys.exit()

print result

# Print result.
print json.dumps({
	"x": result.x.tolist(),
	"slack": result.slack.tolist(),
	"success": result.success,
	"status": result.status,
	"nit": result.nit,
	"message": result.message,
	"fun": result.fun
})

