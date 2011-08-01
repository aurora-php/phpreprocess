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
