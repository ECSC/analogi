<?php

/**
 * A PHP Interface to GNU Plot
 *
 * Copyright (C) 2006 Liu Yi (Eric) <eric.liu.yi (at) gmail.com>
 *
 * Website : http://celeste.cn
 *
 * The main purpose of this program is to facilitate plotting with data
 * generated from program. It is not a *Complete* interface to GNU Plot.
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 **/


// change this if u need
//$GNUPLOT = 'C:\gnuplot\bin\pgnuplot.exe'; // for Windows
$GNUPLOT = "gnuplot";  // for linux

$tempDir = './'; // somewhere we can store the temporary data files

// DONT change the code below if you dont know what you are doing
$IDCounter = 0;

/**
 * PGData is a general data unit for series plotting
 * 
 * It can either be created from an external data file ( in this case, it is simply a wrapper to the file )
 * or created with an empty list of entries ( You can append entries to the list ).
 * 
 * Before it is plotted, if it is created in the 2nd way, it must have its data dumped into a file.
 *
 **/
 
class PGData {
    // privaite variables
    var $filename; // Name of the data file. Can be explicitly specified or automatically generated
    var $DataList; // This is only useful when $filename is not specified
    var $legend; // Title of the data you want to see on the graph

    function PGData($legend = '') {
        $this->legend = $legend;
    }

    /**
     * static method to initialize a data object from an external data file
     * the object is just a wrapper to the file
     **/
    function createFromFile($filename, $legend = '')  {
        $Data = new PGData($legend);
        if (!file_exists($filename) || !is_readable($filename)) {
            print "Error: $filename is not a readable datafile!\n";
            return NULL;
        }
        $Data->filename = $filename;
        return $Data;
    }

    function changeLegend( $legend ) { $this->legend = $legend; }
    
    function addDataEntry( $entry ){
        if (!$filename) $this->DataList[] = $entry;
            else print "Error: Cannot add an entry into file content [ $this->filename ] !\n";
        
    }
    
    function dumpIntoFile( $filename='' ) {
        
        if ($this->filename) { print "Error: Data file exists [ $this->filename ] !\n"; return; }
        global $tempDir, $IDCounter;
        if (!$filename) {
            // generate a file name
            $filename = 'data_'. ( $IDCounter++ ) .'.txt';
            global $toRemove;
            $toRemove[] = $filename;
        }
        $fp = fopen($filename, 'w');
        foreach( $this->DataList as $entry ) fwrite($fp, implode("\t", $entry)."\n");
        fclose($fp);
        $this->filename = $filename; // no longer changeable
    }
}

/**
 * The main class to communicate with GNU Plot
 * It opens a pipe to a GNU Plot process
 * 
 * You can guess the idea from the names of the functions
 * Some of the parameters are explained in the comments
 **/

class GNUPlot {

    // private variables
    var $ph = NULL;
    var $toRemove;
    var $plot;
    var $splot;

    function GNUPlot() {
        global $GNUPLOT;
        $this->ph = popen($GNUPLOT, 'w');
        $this->toRemove = array();
        $this->plot = 'plot';
        $this->splot = 'splot';
    }

    // You can tell from the name of the function.
    function draw2DLine($x1,$y1, $x2, $y2)
    {
       $this->exe( "set arrow from $x1,$y1 to $x2,$y2 nohead\n" );
    }
    function draw3DLine($x1,$y1,$z1, $x2, $y2,$z2)
    {
       $this->exe( "set arrow from $x1,$y1,$z1 to $x2,$y2,$z2 nohead\n" );
    }

    function draw2DArrow($x1,$y1, $x2, $y2)
    {
       $this->exe( "set arrow from $x1,$y1 to $x2,$y2 head\n" );
    }
    function draw3DArrow($x1,$y1,$z1, $x2, $y2,$z2)
    {
       $this->exe( "set arrow from $x1,$y1,$z1 to $x2,$y2,$z2 head\n" );
    }

    function set2DLabel($labeltext, $x, $y, $justify='', $pre='', $extra='' ) 
    {
        // $justify =  {left | center | right}
        // $pre = { first|second|graph|screen }

        $this->exe( "set label \"". $labeltext ."\" at $pre $x,$y $extra\n");
    }
    
    function set3DLabel($labeltext, $x, $y, $z, $justify='', $pre='', $extra='' ) 
    {
        // $justify =  {left | center | right}
        // $pre = { first|second|graph|screen }

        $this->exe( "set label \"". $labeltext ."\" at $pre $x,$y,$z $extra\n");
    }

    function setRange( $dimension, $min, $max, $extra='' ) {
        // $dimension = x, y, z ......
        if (!$dimension) $dimension = 'x';
        $this->exe( "set ${dimension}range [$min:$max] $extra\n");
    }

    // low level set command
    function set( $toSet ) {
        $this->exe( "set $toSet\n");
    }

    function setTitle( $title, $extra='' ) {
        $this->exe( "set title \"$title\" $extra\n");
    }

    // Set label for each axis
    function setDimLabel( $dimension, $text, $extra='' ) {
        // $dimension = x, y, z ......
        $this->exe( "set ${dimension}label \"$text\" $extra\n");
    }

    function setTics( $dimension, $option ) {
        // $dimension = x, y, z ......
        $this->exe( "set ${dimension}tics $option \n" );
    }
    
    function setSize( $x, $y, $extra='' ) {
        // $extra = {{no}square | ratio <r> | noratio}
        $this->exe( "set size $extra $x,$y\n");    
    }

    function plotData(  &$PGData, $method, $using, $axis='', $extra='' ) {
        /**
         * This function is for 2D plotting
         *
         * $method is `lines`, `points`, `linespoints`, `impulses`, `dots`, `steps`, `fsteps`, 
         *              `histeps`, errorbars, `xerrorbars`, `yerrorbars`, `xyerrorbars`, errorlines, 
         *              `xerrorlines`, `yerrorlines`, `xyerrorlines`, `boxes`, `filledcurves`, 
         *              `boxerrorbars`, `boxxyerrorbars`, `financebars`, `candlesticks`, `vectors` or pm3d 
         *
         * $using is an expression controlling which data columns to use and how to use:
         *             Example : $using = " 1:2 " means plotting column 2 against column 1
         *                      $using = " ($1):($2/2)  " means use half of the value of column 2 to plot against column 1
         *            You can introduce in more than 2 or 3 columns to enable styles like errorbars
         **/
        
        $plot = $this->plot;
        if (!$PGData->filename) $PGData->dumpIntoFile();
        if (!$PGData->filename) { print "Error: Empty dataset!\n"; return; }

        $fn = $PGData->filename;
        $title = $PGData->legend;

        if ($axis) $axis = " axis $axes ";
        $this->exe( "$plot '$fn' using $using title \"$title\" with $method  $axis $extra\n");
        //print "$plot '$fn' using $using title \"$title\" with $method  $axis $extra\n";
        $this->plot = 'replot';
        
    }

    function splotData( &$PGData, $method, $using, $extra = '' ) {
        /**
         * This function is for 3D plotting
         *
         */
        $splot = $this->splot;
        if (!$PGData->filename) $PGData->dumpIntoFile();
        if (!$PGData->filename) { print "Error: Empty dataset!\n"; return; }

        $fn = $PGData->filename;
        $title = $PGData->legend;

        $this->exe( "$splot '$fn' using $using title \"$title\" with $method $extra\n");
        $this->splot = 'replot';
        
    }

    function export( $pic_filename ) {
        /**
         * export to $pic_filename
         * the file ext can be png, ps or eps
         */
         
        if (preg_match("/\.png$/", $pic_filename)) $this->exe("set term png\n");
        elseif (preg_match("/\.e?ps$/", $pic_filename)) $this->exe( "set term postscript\n");
        // you can add more options here to support other formats
        else { $this->exe( "set term png\n"); $pic_filename.=".png"; }

        $this->exe( "set output \"$pic_filename\"\n");
        $this->exe( "replot\n" );
    }

    function reset() {
        /**
         * It is not a good idea to use this function in your program
         */
        $this->exe( "reset\n" );
        $this->plot = 'plot';
        $this->splot = 'splot';
    }
    
    function close() {
        flush($this->ph);
        pclose($this->ph);
        //sleep(count($this->toRemove) ); // allow gnu plot to finish so that we can safely remove the data files
        //foreach($this->toRemove as $filename) unlink($filename);
    }

    function exe( $command ) {
        fwrite($this->ph, $command);
    }
    
}


// in case you are using old PHP. just in case
if (!function_exists('file_put_contents')) {
    function file_put_contents( $fn, $contents ) {
        $fp = fopen($fn, 'w');
        fwrite($fp, $contents);
        fclose($fp);
    }
} 
