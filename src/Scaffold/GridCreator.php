<?php

namespace Dcat\Admin\Scaffold;

trait GridCreator
{
    /**
     * @param string $primaryKey
     * @param array $fields
     * @return string
     */
    protected function generateGrid(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps') : $timestamps;

        $rows = [
            "\$grid->column('{$primaryKey}')->sortable();",
        ];

        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            if ($field['name'] == $primaryKey) {
                continue;
            }
            $str = "";
            switch ($field['name']) {
                case 'image':
                    $str = "            \$grid->column('{$field['name']}')->image('', 50);";
                    break;
                case 'images':
                    $str = "            \$grid->column('{$field['name']}')->image('', 50);";
                    break;
                case 'contents':
                    $str = <<<EOF
            \$grid->column('{$field['name']}')->display('预览')->modal(function(){
                return \$this->{$field['name']};
            });
EOF;
                    break;
                case 'editors':
                    $str =  $str = <<<EOF
            \$grid->column('{$field['name']}')->display('预览')->modal(function(){
                return \$this->{$field['name']};
            });
EOF;
                    break;
                default:
                    switch ($field['type']) {
                        case "string":
                            $str = "            \$grid->column('{$field['name']}');";
                            break;
                        case "integer":
                            $str = "            \$grid->column('{$field['name']}')->image('', 50);";
                            break;
                        case "decimal":
                            $str = "            \$grid->column('{$field['name']}')->switch();";
                            break;
                        case "text":
                            $str =  $str = <<<EOF
            \$grid->column('{$field['name']}')->display('预览')->modal(function(){
                return \$this->{$field['name']};
            });
EOF;
                            break;
                        default:
                            $str = "            \$grid->column('{$field['name']}');";
                    }
            }
            $rows[] = $str;
        }
        if ($timestamps) {
            $rows[] = '            $grid->column(\'created_at\');';
            $rows[] = '            $grid->column(\'updated_at\')->sortable();';
        }

        $rows[] = <<<EOF

            \$grid->filter(function (Grid\Filter \$filter) {
                \$filter->equal('$primaryKey');

            });
EOF;

        return implode("\n", $rows);
    }
}
