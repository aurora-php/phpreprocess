Conditions
==========

{ifdef(-test => "1"){
    this shall be included
}}

{ifndef(-test => "1"){
    this shall not be included
}}


This is an inline {ifdef(-test => "1"){
    test
}} condition

{ifdef(-test => $intern){
    This text is only visible, if the variable $intern
    is set to "true". This can for example be achieved 
    by specifying the intern property on command-line:
    
    `-p intern=1`
}}
