<?php

use Illuminate\Database\Seeder;
use App\Field;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Field::create ([
            'field' =>'Arithmetic',
            'description' =>'Fractions, Mental calculation‎, Division and Multiplication, Ratio'
        ]);

        Field::create ([
            'field' => 'Algebra',
            'description' =>'Abstract algebra‎, Elementary algebra‎, Linear algebra‎, Mathematical identities‎, Polynomials‎, Series expansions‎, Symmetric functions‎, Variables'
        ]);

        Field::create ([
            'field' => 'Applied Mathematics',
            'description' =>'Actuarial science, Algorithms, Computational mathematics, Computational science,
				Cryptography‎, Cybernetics, Mathematical economics, Mathematical finance, Mathematical and theoretical biology, Mathematical chemistry,
				Mathematical physics, Mathematical psychology, Mathematics in medicine, Mathematics of music, Operations research, Probability theory‎,
				Algebra of random variables, Central limit theorem, Decision theory, DempsterShafer theory, Ergodic theory, Exotic probabilities,
				Large deviations theory, Probabilistic inequalities, Probability interpretations, Probability journals, Probability theorems,
				Probability theorists, Random matrices, Stochastic algorithms, Stochastic processes, Theory of probability distributions'
        ]);

        Field::create ([
            'field' => 'Analysis and Calculus',
            'description' =>'Calculus, Integral calculus, Limits, Mathematical series, Multivariable calculus, Non-Newtonian calculus, Rates of change'
        ]);

        Field::create ([
            'field' => 'Probability and Statistics',
            'description' =>'Operations research, Probability, Randomness, Statistics'
        ]);

        Field::create ([
            'field' => 'Measurement',
            'description' =>'Measurement'
        ]);

        Field::create ([
            'field' => 'Geometry',
            'description' =>'Geometry'
        ]);

        Field::create ([
            'field' => 'Logic',
            'description' =>'Mathematical Logic, Proofing, Mathematical Induction, Set Theory'
        ]);

        Field::create ([
            'field' => 'Topology',
            'description' =>'Deals with the properties of a figure that do not change when the figure is continuously deformed. The main areas are point set topology (or general topology), algebraic topology, and the topology of manifolds.'
        ]);

        Field::create ([
        	'field' => 'Numerical Analysis',
        	'description' => 'The study of iterative methods and algorithms for approximately solving problems to a specified error bound. Includes numerical differentiation, numerical integration and numerical methods'
        ]);

        Field::create ([
        	'field' => 'Trigonometry',
        	'description' => 'Trigonometry'
        ]);
    }
}