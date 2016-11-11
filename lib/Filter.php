<?php
class Filter
{
    public function isAdmin()
    {
        return System::user()->hasSignin && System::user()->permission->editSite;
    }
}
