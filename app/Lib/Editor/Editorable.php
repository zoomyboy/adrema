<?php

namespace App\Lib\Editor;

interface Editorable
{
    public function toEditorData(): EditorData;
}
