AnaLogi v1.0
Copyright (C) 2012 ECSC Ltd.


= Information about AnaLogi =
 
'Analytical Log Interface' built to sit on top of OSSEC (built on OSSEC 2.6)

Written for inhouse analysis work, released under GPL to give something back.

AnaLogi was built for OSSEC 2.6 and requires 0 modifications to OSSEC or the
database schema that ships with OSSEC.  AnaLogi requires a Webserver sporting
PHP and MySQL.

Available from:
https://github.com/downloads/ECSC/analogi/


= Notes =

Ossec is used for internal servers, therefore server names are treated as trusted and are not filtered for security with in this project.  For the same reason user input on the details page is not filtered... if you want to inject SQL, go ahead, you are the Sys Admin after all.

Log data IS treated as UNTRUSTED, and is validated before dumping to screen.

This was written and tested on Virtual Machine, quad core, 4GB ram using a
database with currently 1.2million alerts and 10 servers and performs fine.

If the interface gets slow over time you may want to consider your data
retention period in the database and clean events out from time to time.


= Thanks / Links = 

amCharts
http://www.amcharts.com/

Sortable
http://www.kryogenix.org/code/browser/sorttable/

Famfamfam Icons
http://www.famfamfam.com/lab/icons/silk/

Hover text
http://www.spiceupyourblog.com/2011/05/simple-css-only-tooltip-descriptions.html

Show Hide Div
http://www.randomsnippets.com/2011/04/10/how-to-hide-show-or-toggle-your-div-with-jquery/

Christian Beer, Fabien Bourdaire, Matthew Hall, Max Batsurin
For help with bug fixing and general support

FreeGeoIP.net
http://freegeoip.net/static/index.html
(Including maxmind.com and google maps)

And last, but certainly not least, OSSEC/Dan!

