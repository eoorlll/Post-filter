<?php
/**
 * Get Current URL
 */
function get_current_url()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url      = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}

/**
 * Get Filter URL
 */
function get_filter_url( $filter_name, $termID )
{
    $current_url = get_current_url();

    /**
     * Using the explode() function, split the link into two lines and put them in an array
     * If the link does not contain an HTTP request, then we add an empty string to the array using the array_pad() function
     * Using the list() function, we assign values from an array to variables: $urlpart - link without HTTP request, $qspart - HTTP request as a string
    */
    list($urlpart, $qspart) = array_pad(explode('?', $current_url), 2, '');

    /**
     * The parse_str() function parses the $qspart query string and returns $qsvars array with variables
    */
    parse_str($qspart, $qsvars);

    /**
     * If the HTTP request contains a filter name
    */
    if( isset( $_GET[$filter_name] ) ) {
        
        /**
         * Splitting filter values
        */
        $param_arr = explode(',', $qsvars[$filter_name]);

        /**
         * Counting the length of $param_arr array
        */
        $param_arr_count = count($param_arr);

        /**
         * Getting the key of the current taxonomy in the request
        */
        $key = array_search($termID, $param_arr, true);


        if( $param_arr_count === 1 && $key !== false ) {

            /**
             * If there is only one value in the query and this value is equal to the current taxonomy, then remove filter from the query
            */
            unset($qsvars[$filter_name]);

        } elseif( $param_arr_count !== 1 && $key !== false ) {

            /**
             * If there is more than one value in the request, then only the current value is removed from the request
            */
            array_splice($param_arr, $key, 1);
            $qsvars[$filter_name] = implode(',', $param_arr);

        } else {

            /**
             * If the current taxonomy is not in the request, then add it
            */
            $param_arr[] = $termID;
            $qsvars[$filter_name] = implode(',', $param_arr);

        }
    } else {
        /**
         * If the HTTP request does not contain the filter name, then add it to the request with the taxonomy ID value
        */
        $qsvars[$filter_name] = $termID;
    }

    /**
     * Assembling the array back into an HTTP request
    */
    $newqs = http_build_query($qsvars);

    /**
     * Return the link
    */
    return $urlpart . '?' . $newqs;
}