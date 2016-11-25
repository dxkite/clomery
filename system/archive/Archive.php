<?php
namespace archive;

interface Archive
{
    function getFeilds():array;
    function getAvailableFields():array;
    function tableCreator():string;
    function sqlCreate():Statement;
    function sqlRetrieve(Condition $condition):Statement;
    function sqlUpdate(Condition $condition):Statement;
    function sqlDelete(Condition $condition):Statement;
}