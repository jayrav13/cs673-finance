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
import sys

# print sys.argv

# Portfolio
portfolio = [
	[ -2.05, 657.48, 33.39, -31.13, 0.72,  .20,   43.96, 6.55,  -10.88 ], # Expected Return
	[ 1.261, 0.955,  1.105, 0.868,  0.623, 1.106, 0.604, 0.983,  0.644 ], # Beta Values
	[ 1, 1, 2, 1, 1, 1, 1, 2, 1] # Buckets
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
	0.86946803,

	# Portfolio balance - USD vs INR
	0.7
]

# Set bounds for weights (must range from 0 to 1)
bounds = (
	(0, 1),
	(0, 1),
	(0, 1),
	(0, 1),
	(0, 1),
	(0, 1),
	(0, 1),
	(0, 1),
	(0, 1)
)

# Solve the linear function!
result = linprog(c, A_eq=A, b_eq=b, bounds=bounds, options={"disp": True}, method="simplex")

# Print result.
print result

