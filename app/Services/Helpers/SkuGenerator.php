<?php

namespace App\Services\Helpers;

class SkuGenerator
{
    // this example is reference from
    // https://stackoverflow.com/questions/39748966/creating-sku-codes-using-php

    public function __construct()
    {
        // Possible variants
        $variants = array(
            'brand' => array(
                // the first value in our array is our SKU identifier, this will be used to create our unqiue SKU code
                // the second value is a nice name, description if you will
                array('AP', 'Apple'),
                array('BA', 'Banana'),
                array('PE', 'Pear'),
            ),
            'color' => array(
                array('RE', 'Red'),
                array('GR', 'Green'),
                array('BL', 'Blue'),
            ),
        );

        // Rules for combinations I dont want
        $disallow = array(
            array('brand' => 'AP', 'color' => 'GR'), // No green apples
            array('brand' => 'AP', 'color' => 'RE'), // No red apples
            array('brand' => 'PE', 'color' => 'BL'), // No blue pears
        );
    }

    /**
     * @param $productId
     * @param array $variants
     * @param array $disallow
     * @return array
     */
    public static function generate($productId, array $variants = [], array $disallow = [])
    {
        // First lets get all of the different permutations = cartesian product
        $permutations = self::permutate($variants);

        // Now lets get rid of the pesky combinations we don't want
        $filtered     = self::squelch($permutations, $disallow);

        // Finally we can generate some SKU codes using the $productId as the prefix
        // this assumes you want to reuse this code for different products
        return  self::skuify($productId, $filtered);
    }

    /**
     * @param array $variants
     * @return array|array[]
     */
    public static function permutate(array $variants)
    {
        // filter out empty values
        // This is the cartesian product code
        $input  = array_filter($variants);
        $result = array(array());
        foreach ($input as $key => $values) {
            $append = array();
            foreach($result as $product) {
                foreach($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }
            $result = $append;
        }

        return $result;
    }

    /**
     * @param array $permutations
     * @param array $rules
     * @return array
     */
    public static function squelch(array $permutations, array $rules)
    {
        // We need to loop over the differnt permutations we have generated
        foreach ($permutations as $per => $values) {
            $valid = true;
            $test  = array();
            // Using the values, we build up a comparison array to use against the rules
            foreach ($values as $id => $val) {
                // Add the KEY from the value to the test array, we're trying to make an
                // array which is the same as our rule
                $test[$id] = $val[0];
            }
            // Now lets check all of our rules against our new test array
            foreach ($rules as $rule) {
                // We use array_diff to get an array of differences, then count this array
                // if the count is zero, then there are no differences and our test matches
                // the rule exactly, which means our permutation is invalid
                if (count(array_diff($rule, $test)) <= 0) {
                    $valid = false;
                }
            }
            // If we found it was an invalid permutation, we need to remove it from our data
            if (!$valid) {
                unset($permutations[$per]);
            }
        }
        // return the permutations, with the bad combinations removed
        return $permutations;
    }

    /**
     * @param $productId
     * @param array $variants
     * @return array
     */
    public static function skuify($productId, array $variants)
    {
        // Lets create a new array to store our codes
        $skus = array();

        // For each of the permutations we generated
        foreach ($variants as $variant) {
            $ids = array();
            // Get the ids (which are the first values) and add them to an array
            foreach ($variant as $vals) {
                $ids[] = $vals[0];
            }

            // Now we create our SKU code using the ids we got from our values. First lets use the
            // product id as our prefix, implode will join all the values in our array together using
            // the separator argument givem `-`. This creates our new SKU key, and we store the original
            // variant as its value
            $skus[$productId . '-' . implode('-', $ids)] = $variant;
            // The bit above can be modified to generate the skues in a different way. It's a case of
            // dumping out our variant data and trying to figure what you want to do with it.
        }
        // finall we return our skus
        return $skus;
    }
}
