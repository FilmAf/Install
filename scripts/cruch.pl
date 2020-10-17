#! /usr/bin/perl -w

#
# This script compresses js code by removing coments and extra spacing
#
# Originally from http://www.bazon.net/mishoo/articles.epl?art_id=209
# 
# It does not support (') (") and spaces inside a RegExp (/___/).
# These need to be replaced with \x27 \x22 and \s or \x20 respectivelly
#
# To find them:
#   grep "/" my.js | grep " " | egrep -i "exp|match|replace"
#   grep "/" my.js | grep "'" | egrep -i "exp|match|replace"
#   grep "/" my.js | grep '"' | egrep -i "exp|match|replace"
#

my $file = $ARGV[0];
my $comment = '';
my $content = '';

open(FILE, "<$file");
undef $/;
$content = <FILE>;
close(FILE);

if ($content =~ s#^\s*(/\*.*?\*/)##s or $content =~ s#^\s*(//.*?)\n\s*[^/]##s) {
  $comment = "$1\n";
  }

# *** EH preserving AJAX object initialization
	$content =~ s/\/\*\x40cc_on/___cc_on___/gs;
	$content =~ s/\x40\*\//___cc_off___/gs;

# removing C/C++ - style comments:
$content =~ s#/\*[^*]*\*+([^/*][^*]*\*+)*/|//[^\n]*|("(\\.|[^"\\])*"|'(\\.|[^'\\])*'|.[^/"'\\]*)#$2#gs;

# *** EH preserving '+' and "+"
	$content =~ s/\'\+\'/\'__\+__\'/gs;
	$content =~ s/\"\+\"/\"__\+__\"/gs;

# save string literals
my @strings = ();
$content =~ s/("(\\.|[^"\\])*"|'(\\.|[^'\\])*')/push(@strings, "$1");'__CMPRSTR_'.$#strings.'__';/egs;

# *** EH preserving else's
	$content =~ s/else\s+/else___/gs;

# remove C-style comments
$content =~ s#/\*.*?\*/##gs;
# remove C++-style comments
$content =~ s#//.*?\n##gs;
# removing leading/trailing whitespace:
$content =~ s#(?:(?:^|\n)\s+|\s+(?:$|\n))##gs;
# removing newlines:
$content =~ s#\r?\n##gs;

# removing other whitespace (between operators, etc.) (regexp-s stolen from Mike Hall's JS Crunchinator)
$content =~ s/\s+/ /gs;         # condensing whitespace
$content =~ s/\s([\x21\x25\x26\x28\x29\x2a\x2b\x2c\x2d\x2f\x3a\x3b\x3c\x3d\x3e\x3f\x5b\x5d\x5c\x7b\x7c\x7d\x7e])/$1/gs;
$content =~ s/([\x21\x25\x26\x28\x29\x2a\x2b\x2c\x2d\x2f\x3a\x3b\x3c\x3d\x3e\x3f\x5b\x5d\x5c\x7b\x7c\x7d\x7e])\s/$1/gs;

# *** EH restoring else's
	$content =~ s/else___/else /gs;
	$content =~ s/else {/else{/gs;

# restore string literals
$content =~ s/__CMPRSTR_([0-9]+)__/$strings[$1]/egs;

# *** EH compressing "a"+"b" to "ab"
	$content =~ s/\"\+\"//gs;
	$content =~ s/\'\+\'//gs;

# *** EH restoring '+' and "+"
	$content =~ s/\'__\+__\'/\'\+\'/gs;
	$content =~ s/\"__\+__\"/\"\+\"/gs;

# *** EH preserving AJAX object initialization
	$content =~ s/___cc_on___/\x0a\/\*\x40cc_on\x0a/gs;
	$content =~ s/___cc_off___/\x0a\x40\*\/\x0a/gs;
	  
print $comment, $content;

