<?php

function get_user_name_by_id(int $id)
{
    return cn\atd3\user\Manager::ids2name([$id])[$id]??null;
}
