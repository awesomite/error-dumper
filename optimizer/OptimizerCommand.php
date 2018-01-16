<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Optimizer;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
class OptimizerCommand
{
    /**
     * @param resource $output
     */
    public function run($output)
    {
        $finder = new Finder();
        $finder
            ->in(\implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'templates')))
            ->name('*.twig')
            ->depth('== 0')
        ;
        
        \fputs($output, "Processing files...\n");
        
        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            \fputs($output, "  {$file->getPathname()}\n");

            $optimizedName = \realpath($file->getPath()) . '_dist' . DIRECTORY_SEPARATOR . $file->getFilename();
            
            \file_put_contents(
                $optimizedName,
                $this->optimize($file->getContents())
            );
        }
    }
    
    private function optimize($input)
    {
        $output = $input;

        $output = \trim($output);

        $output = $this->removeWhiteSpacesBetween('>', '<', $output);
        $output = $this->removeWhiteSpacesBetween('>', '{', $output);
        $output = $this->removeWhiteSpacesBetween('}', '<', $output);
        $output = $this->removeWhiteSpacesBetween('}', '>', $output);
        $output = $this->removeWhiteSpacesBetween('}', '{', $output);
        $output = $this->removeWhiteSpacesBetween('%}', '{%', $output);
        $output = $this->removeWhiteSpacesBetween('}}', '{%', $output);
        $output = $this->removeWhiteSpacesBetween('"', '>', $output);
        $output = $this->removeWhiteSpacesBetween('"', '/>', $output);

        // '<script   src' => '<script src'
        $output = \preg_replace_callback(
            '/(?<first>[a-zA-Z"])\s{2,}(?<second>[a-zA-Z"])/s',
            function ($match) {
                return $match['first'] . ' ' . $match['second'];
            },
            $output
        );
        
        // "{{ '   ' }}" => '   '
        $output = \preg_replace_callback(
            '/\{\{\s*\'(?<spaces>\s+)\'\s*\}\}/',
            function ($match) {
                return $match['spaces'];
            },
            $output
        );

        $output = $this->removeWhiteSpacesBetween('>', 'Generated by', $output);
        
        // '   &bull;' => ' &bull;'
        $output = \preg_replace_callback(
            '/(?<pre>\s*)(?<entity>&[a-z0-9]+);(?<post>\s*)/s',
                function ($matches) {
                    $pre = !empty($matches['pre']) ? ' ' : '';
                    $post = !empty($matches['post']) ? ' ' : '';
                
                    return $pre . $matches['entity'] . $post;
                },
            $output
        );
        
        // css
        $output = \preg_replace_callback(
            '/(?<open>\<style[^>]*>)(?<inner>.+)(?<close>\<\/style>)/s',
            function ($matches) {
                return $matches['open'] . \preg_replace('/\s{2,}/', ' ', $matches['inner']) . $matches['close'];
            },
            $output
        );

        $toReplace = array(
            '%} {%' => '%}{%',
        );
        $output = \str_replace(\array_keys($toReplace), \array_values($toReplace), $output);
        
        $output = \trim($output) . "\n";

        return $output;
    }

    private function removeWhiteSpacesBetween($left, $right, $input)
    {
        $regex = '/' . preg_quote($left, '/') . '\s{1,}' . preg_quote($right, '/') . '/';

        return preg_replace($regex, $left . $right, $input);
    }
}
