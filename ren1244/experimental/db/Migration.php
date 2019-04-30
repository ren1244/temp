<?php
namespace ren1244\db;

interface Migration
{
    public function up();
    public function down();
}