<?php

include('php/gnuplot.php');

$p = new GNUPlot();
$p->draw2DLine( 0,0, 1,1);
$data = new PGData('test Data');
$data->addDataEntry( array(1, 2) );
$data->addDataEntry( array(2, 3) );
$data->addDataEntry( array(3, 4) );
$data->addDataEntry( array(4, 4) );
$data->addDataEntry( array(5, 3) );

#demoSampleFile();
$p->setTitle("2D Test");
$data2 = PGData::createFromFile('plot1.txt', 'data set II');

$p->plotData( $data, 'lines', '1:($2)' );
$p->set2DLabel("2D Label", 1,1 );
$p->plotData( $data2, 'linespoints', '($1/20):($2*2)' );

$data2->changeLegend( 'replot II' );

$p->plotData( $data2, 'boxes', '($1/20):($2)' );

//$p->set("autoscale");
$p->setRange('y', 0, 5);
$p->setSize( 0.6, 0.6 );
$p->export('test2D.png');

$p->close();


#function demoSampleFile() {
#    $fp = file_put_contents('plot1.txt', 
#"10    0.093589504197705
#20    0.18763678062534
#30    0.28007895516094
#40    0.3772850801436
#50    0.45641177158072
#60    0.5483119759646
#70    0.64713177119153
#80    0.73679384002403
#90    0.82093219662338
#100    0.89928340743387
#110    0.9330042604089
#120    0.91835632225303
#130    0.77081887392486
#140    0.56138136507401
#150    0.35419806755938
#160    0.19338611559021
#170    0.097598541367033
#180    0.048300839801159
#");
#}

