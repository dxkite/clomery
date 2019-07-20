<?php


namespace clomery\main\provider;


use clomery\content\provider\ContentProvider;
use clomery\main\table\ArticleTable;
use clomery\main\table\TagRelationTable;
use clomery\main\table\TagTable;


class ArticleProvider extends ContentProvider
{
    public function __construct()
    {
        parent::__construct(new ArticleTable(), new TagTable(), new TagRelationTable());
    }
}