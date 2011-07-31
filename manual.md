PHPreProcess
============

This is a script intended to work as proprocessor for arbitrary files. I wrote it to preprocess
markdown files before feeding them to pandoc.

Syntax
------

./phpreprocess [-i directory-for-includes] [-p ]

Configuration File
------------------

PHPreProcess tries to load the file ~/.PHPreProcess.ini to read it's configuration. The configuration
file registeres the tools available to PHPreProcess for preprocessing special markdown content.

    [command-name]
    command='command-executable including default-parameters'
    
Example:


Syntax
------

The preprocessor syntax is quite easy and supports "commands" and "constants".

### Commands

A command consists of a command-name and an arbitrary amount of parameters:

    {function(... [, ...])}

Some commands may require content as STDIN. The content to send to STDIN of the executed command
is definied by after the parameter-list included in a pair of additional '{', '}' paranthesis:

    {function(... [, ...]){
        ...
    }}

The parameters are either named or unnamed.

#### Examples

Include "readme.md" in the current document:

    \{include("readme.md")\}
    
Execute 'asciidia' to generate a bitmap representation of some ASCII diagram:

    {asciidia(-type => "-", -o => "./figures/", -t => "diagram")}

Note, that -- as mentioned in the previous section -- default parameters may be already specified in 
the command configuration in the configuration file.


### Constants

The preprocessor has the following built-in constants:

    {__dir__}


Built-in functions
------------------

The built-in functionality is very limited. Currently only a few functions are available. Any 
further functionality would have to be built as external scripts configured with the PHPreProcess.ini
configuration file. Note, that it is possible to overwrite the built-in functions with the the
configuration file. So a registered tool 'include' in the configuration would override the built-in
function root.

### date

This function allows to include the current datetime in a document. A formatting parameter is allowed
to specify the format of the output. The implementation of 'date' uses the PHP method *strftime* and 
therefore accepts the same formatting placeholders as specified in the PHP documentation:

    http://www.php.net/manual/en/function.strftime.php

#### Example

    {date("%Y-%m-%d %H:%M:%S")}


### include

This function allows to merge multiple markdown files.

    \{include("...")\}
    
