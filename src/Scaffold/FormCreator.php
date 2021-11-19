<?php

namespace Dcat\Admin\Scaffold;

trait FormCreator
{
    /**
     * @param  string  $primaryKey
     * @param  array  $fields
     * @param  bool  $timestamps
     * @return string
     */
    protected function generateForm(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps') : $timestamps;

        $rows = [
            <<<EOF
\$form->display('{$primaryKey}');
EOF

        ];

        foreach ($fields as $field) {
            $nullable = false;
            $default = $field['default'];
            $end = $default != null ? "->default('$default');" : ";";

            if (empty($field['name'])) {
                continue;
            }

            if ($field['name'] == $primaryKey) {
                continue;
            }
            if (isset($field['nullable'])) {
                $nullable = true;
            }

            $str = "";
            switch ($field['name']) {
                case 'image':
                    $str =  "            \$form->image('{$field['name']}')->autoUpload()->saveFullUrl()";
                    break;
                case 'images':
                    $str =  "            \$form->multipleImage('{$field['name']}')->autoUpload()->saveFullUrl()";
                    break;
                case 'file':
                    $str =  "            \$form->file('{$field['name']}')->autoUpload()->saveFullUrl()";
                    break;
                case 'files':
                    $str =  "            \$form->multipleFile('{$field['name']}')->autoUpload()->saveFullUrl()";
                    break;
                case 'contents':
                    $str =  "            \$form->editor('{$field['name']}')";
                    break;
                case 'editors':
                    $str =  "            \$form->editor('{$field['name']}')";
                    break;
                default:
                    switch ($field['type']) {
                        case "string":
                            $str =  "            \$form->text('{$field['name']}')";
                            break;
                        case "integer":
                            $str =  "            \$form->number('{$field['name']}')";
                            break;
                        case "float":
                            $str =  "            \$form->rate('{$field['name']}')";
                            break;
                        case "decimal":
                            $str =  "            \$form->switch('{$field['name']}')";
                            break;
                        case "text":
                            $str =  "            \$form->editor('{$field['name']}')";
                            break;
                        default:
                            $str =  "            \$form->text('{$field['name']}')";
                    }
            }
            if (!$nullable) {
                $str .= "->required()";
            }
            $str .= $end;

            $rows[] = $str;
        }
        if ($timestamps) {
            $rows[] = <<<'EOF'

            $form->display('created_at');
            $form->display('updated_at');
EOF;
        }

        return implode("\n", $rows);
    }
}
