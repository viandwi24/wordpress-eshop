<?php

if (!function_exists('eshop_asset')) 
{    
    /**
     * Get the path of template
     *
     * @param  string $path
     * @return string
     */
    function eshop_asset($path): string
    {
        return get_template_directory_uri() . '/' . $path;
    }
}

if (!function_exists('eshop_assets') && function_exists('eshop_asset')) 
{    
    /**
     * Get assets folder
     *
     * @param  string $path
     * @return string
     */
    function eshop_assets($path): string
    {
        return eshop_asset('assets/' . $path);
    }
}