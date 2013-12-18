#!/bin/bash
# $Id: phpdoc.sh,v 1.5 2013/03/08 16:01:12 fabriziomonti Exp $ 

#/**
#  * makedoc - PHPDocumentor script to save your settings
#  * 
#  * Put this file inside your PHP project homedir, edit its variables and run whenever you wants to
#  * re/make your project documentation.
#  * 
#  * The version of this file is the version of PHPDocumentor it is compatible.
#  * 
#  * It simples run phpdoc with the parameters you set in this file.
#  * NOTE: Do not add spaces after bash variables.
#  *
#  * @copyright         makedoc.sh is part of PHPDocumentor project {@link http://freshmeat.net/projects/phpdocu/} and its LGPL
#  * @author            Roberto Berto <darkelder (inside) users (dot) sourceforge (dot) net>
#  * @version           Release-1.1.0
#  */


##############################
# should be edited
##############################

#/**
#  * title of generated documentation, default is 'Generated Documentation'
#  * 
#  * @var               string TITLE
#  */
TITLE="waDocumentazione 3.0"

#/** 
#  * name to use for the default package. If not specified, uses 'default'
#  *
#  * @var               string PACKAGES
#  */
PACKAGES="waDocumentazione"

#/** 
#  * name of a directory(s) to parse directory1,directory2
#  * $PWD is the directory where makedoc.sh 
#  *
#  * @var               string PATH_PROJECT
#  */
PATH_PROJECT=../

#/**
#  * path of PHPDoc executable
#  *
#  * @var               string PATH_PHPDOC
#  */
PATH_PHPDOC=phpdoc

#/**
#  * where documentation will be put
#  *
#  * @var               string PATH_DOCS
#  */
PATH_DOCS=$PATH_PROJECT/doc

#/**
#  * ignore file list
#  *
#  * @var               string IGNORE
#  */
IGNORE=db/,doc/,doc_builder/,wadocapp/

#/**
#  * what outputformat to use (html/pdf)
#  *
#  * @var               string OUTPUTFORMAT
#  */
OUTPUTFORMAT=HTML

#/** 
#  * converter to be used
#  *
#  * @var               string CONVERTER
#  */
CONVERTER=Smarty/waLibs

#/**
#  * template to use
#  *
#  * @var               string TEMPLATE
#  */
TEMPLATE=waLibs

#/**
#  * parse elements marked as private
#  *
#  * @var               bool (on/off)           PRIVATE
#  */
PRIVATE=off

# copia il file "copertina" nella directory di  parsing
cp $PATH_PROJECT/doc_builder/index.html $PATH_PROJECT/$PACKAGES.html

# make documentation
$PATH_PHPDOC -d $PATH_PROJECT -t $PATH_DOCS -ti "$TITLE" -dn $PACKAGES \
-o $OUTPUTFORMAT:$CONVERTER:$TEMPLATE -pp $PRIVATE -i $IGNORE

#rimuove il file copertina dalla directory di parsing
rm $PATH_PROJECT/$PACKAGES.html


# vim: set expandtab :
