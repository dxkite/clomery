<?php
namespace archive;

interface Archive
{
    function getFeilds():array;
    function getAvaiableFields():array;
    function tableCreator():string;
    function sqlCreate():string;
    function sqlRetrieve(Condition $condition):string;
    function sqlDelete();
    function sqlUpdate();
}