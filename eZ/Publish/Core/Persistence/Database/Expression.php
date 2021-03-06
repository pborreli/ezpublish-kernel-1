<?php
/**
 * File containing an interface for the database abstractions
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence\Database;

/**
 * Interface for generation of all the expressions for database abstractions.
 */
interface Expression
{
    /**
     * Returns the SQL to bind logical expressions together using a logical or.
     *
     * lOr() accepts an arbitrary number of parameters. Each parameter
     * must contain a logical expression or an array with logical expressions.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $e = $q->expr;
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $e->lOr( $e->eq( 'id', $q->bindValue( 1 ) ),
     *                                    $e->eq( 'id', $q->bindValue( 2 ) ) ) );
     * </code>
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if called with no parameters.
     * @return string a logical expression
     */
    public function lOr();

    /**
     * Returns the SQL to bind logical expressions together using a logical and.
     *
     * lAnd() accepts an arbitrary number of parameters. Each parameter
     * must contain a logical expression or an array with logical expressions.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $e = $q->expr;
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $e->lAnd( $e->eq( 'id', $q->bindValue( 1 ) ),
     *                                     $e->eq( 'id', $q->bindValue( 2 ) ) ) );
     * </code>
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if called with no parameters.
     * @return string a logical expression
     */
    public function lAnd();

    /**
     * Returns the SQL for a logical not, negating the $expression.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $e = $q->expr;
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $e->eq( 'id', $e->not( 'null' ) ) );
     * </code>
     *
     * @param string $expression
     * @return string a logical expression
     */
    public function not( $expression );

    /**
     * Returns the SQL to add values or expressions together.
     *
     * add() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->add( 'id', 2 )  );
     * </code>
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function add();

    /**
     * Returns the SQL to subtract values or expressions from eachother.
     *
     * subtract() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->subtract( 'id', 2 )  );
     * </code>
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function sub();

    /**
     * Returns the SQL to multiply values or expressions by eachother.
     *
     * multiply() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->multiply( 'id', 2 )  );
     * </code>
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function mul();

    /**
     * Returns the SQL to divide values or expressions by eachother.
     *
     * divide() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->divide( 'id', 2 )  );
     * </code>
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function div();

    /**
     * Returns the SQL to check if two values are equal.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->eq( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function eq( $value1, $value2 );

    /**
     * Returns the SQL to check if two values are unequal.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->neq( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function neq( $value1, $value2 );

    /**
     * Returns the SQL to check if one value is greater than another value.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->gt( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function gt( $value1, $value2 );

    /**
     * Returns the SQL to check if one value is greater than or equal to
     * another value.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->gte( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function gte( $value1, $value2 );

    /**
     * Returns the SQL to check if one value is less than another value.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->lt( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function lt( $value1, $value2 );

    /**
     * Returns the SQL to check if one value is less than or equal to
     * another value.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->lte( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function lte( $value1, $value2 );

    /**
     * Returns the SQL to check if a value is one in a set of
     * given values..
     *
     * in() accepts an arbitrary number of parameters. The first parameter
     * must always specify the value that should be matched against. Successive
     * parameters must contain a logical expression or an array with logical
     * expressions.  These expressions will be matched against the first
     * parameter.
     *
     * Example:
     * <code>
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->in( 'id', 1, 2, 3 ) );
     * </code>
     *
     * Optimization note: Call setQuotingValues( false ) before using in() with
     * big lists of numeric parameters. This avoid redundant quoting of numbers
     * in resulting SQL query and saves time of converting strings to
     * numbers inside RDBMS.
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if called with less than two
     *         parameters.
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException if the 2nd parameter is an
     *         empty array.
     * @param string $column the value that should be matched against
     * @param string|array(string) $... values that will be matched against $column
     * @return string logical expression
     */
    public function in( $column );

    /**
     * Returns SQL that checks if a expression is null.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->isNull( 'id' ) );
     * </code>
     *
     * @param string $expression the expression that should be compared to null
     * @return string logical expression
     */
    public function isNull( $expression );

    /**
     * Returns SQL that checks if an expression evaluates to a value between
     * two values.
     *
     * The parameter $expression is checked if it is between $value1 and $value2.
     *
     * Note: There is a slight difference in the way BETWEEN works on some databases.
     * http://www.w3schools.com/sql/sql_between.asp. If you want complete database
     * independence you should avoid using between().
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->between( 'id', $q->bindValue( 1 ), $q->bindValue( 5 ) ) );
     * </code>
     *
     * @param string $expression the value to compare to
     * @param string $value1 the lower value to compare with
     * @param string $value2 the higher value to compare with
     * @return string logical expression
     */
    public function between( $expression, $value1, $value2 );

    /**
     * Match a partial string in a column.
     *
     * Like will look for the pattern in the column given. Like accepts
     * the wildcards '_' matching a single character and '%' matching
     * any number of characters.
     *
     * @param string $expression the name of the expression to match on
     * @param string $pattern the pattern to match with.
     */
    public function like( $expression, $pattern );

    /**
     * Returns the average value of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function avg( $column );

    /**
     * Returns the number of rows (without a NULL value) of a column
     *
     * If a '*' is used instead of a column the number of selected rows
     * is returned.
     *
     * @param string $column the column to use
     * @return string
     */
    public function count( $column );

    /**
     * Returns the highest value of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function max( $column );

    /**
     * Returns the lowest value of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function min( $column );

    /**
     * Returns the total sum of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function sum( $column );

    /**
     * Returns the length of text field $column
     *
     * @param string $column
     * @return string
     */
    public function length( $column );

    /**
     * Rounds a numeric field to the number of decimals specified.
     *
     * @param string $column
     * @param int $decimals
     * @return string
     */
    public function round( $column, $decimals );

    /**
     * Returns the remainder of the division operation
     * $expression1 / $expression2.
     *
     * @param string $expression1
     * @param string $expression2
     * @return string
     */
    public function mod( $expression1, $expression2 );

    /**
     * Returns the current system date and time in the database internal
     * format.
     *
     * @return string
     */
    public function now();

    // string functions
    /**
     * Returns part of a string.
     *
     * Note: Not SQL92, but common functionality.
     *
     * @param string $value the target $value the string or the string column.
     * @param int $from extract from this characeter.
     * @param int $len extract this amount of characters.
     * @return string sql that extracts part of a string.
     */
    public function subString( $value, $from, $len = null );

    /**
     * Returns a series of strings concatinated
     *
     * concat() accepts an arbitrary number of parameters. Each parameter
     * must contain an expression or an array with expressions.
     *
     * @param string|array(string) $... strings that will be concatinated.
     */
    public function concat();

    /**
     * Returns the SQL to locate the position of the first occurrence of a substring
     *
     * @param string $substr
     * @param string $value
     * @return string
     */
    public function position( $substr, $value );

    /**
     * Returns the SQL to change all characters to lowercase
     *
     * @param string $value
     * @return string
     */
    public function lower( $value );

    /**
     * Returns the SQL to change all characters to uppercase
     *
     * @param string $value
     * @return string
     */
    public function upper( $value );

    /**
     * Returns the SQL that performs the bitwise AND on two values.
     *
     * @param string $value1
     * @param string $value2
     * @return string
     */
    public function bitAnd( $value1, $value2 );

    /**
     * Returns the SQL that performs the bitwise OR on two values.
     *
     * @param string $value1
     * @param string $value2
     * @return string
     */
    public function bitOr( $value1, $value2 );

    /**
     * Returns a searched CASE statement.
     *
     * Accepts an arbitrary number of parameters.
     * The first parameter (array) must always be specified, the last
     * parameter (string) specifies the ELSE result.
     *
     * Example:
     * <code>
     * $q = $dbHandler->createSelectQuery();
     * $q->select(
     *      $q->expr->searchedCase(
     *            array( $q->expr->gte( 'column1', 20 ), 'column1' )
     *          , array( $q->expr->gte( 'column2', 50 ), 'column2' )
     *          , 'column3'
     *      )
     *  )
     *     ->from( 'table' );
     * </code>
     *
     * @throws \eZ\Publish\Core\Persistence\Database\QueryException
     * @return string
     */
    public function searchedCase();
}
