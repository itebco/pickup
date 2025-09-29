<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class RemoveSpecialCharacters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $disableCharacters = Config::get('setting.disable_characters', []);
        if (!empty($disableCharacters)) {
            $this->sanitizeRequestData($request, $disableCharacters);
        }

        return $next($request);
    }

    /**
     * Recursively sanitize request data
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array  $disableCharacters
     * @return void
     */
    private function sanitizeRequestData(Request $request, array $disableCharacters)
    {
        $data = $request->all();
        $sanitizedData = $this->sanitizeArray($data, $disableCharacters);
        $request->replace($sanitizedData);
    }

    /**
     * Recursively sanitize an array
     *
     * @param  mixed  $data
     * @param  array  $disableCharacters
     * @return mixed
     */
    private function sanitizeArray($data, array $disableCharacters)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeArray($value, $disableCharacters);
            }
            return $data;
        } elseif (is_string($data)) {
            return $this->removeSpecialCharacters($data, $disableCharacters);
        }

        return $data;
    }

    /**
     * Remove special characters from a string
     *
     * @param  string  $str
     * @param  array  $disableCharacters
     * @return string
     */
    private function removeSpecialCharacters($str, array $disableCharacters)
    {
        if (empty($disableCharacters)) {
            return $str;
        }

        // Create a regex pattern to match any of the disabled characters
        $pattern = '/[' . implode('', array_map(function($char) {
            // Escape special regex characters
            return preg_quote($char, '/');
        }, $disableCharacters)) . ']/u';

        return preg_replace($pattern, '', $str);
    }
}
