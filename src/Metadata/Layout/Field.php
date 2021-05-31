<?php

namespace Antares\Crud\Metadata\Layout;

class Field extends AbstractLayout
{
    /**
     * Class constructor
     *
     * @param array $data
     * @param array $prototypes
     */
    public function __construct(array $data = [])
    {
        $data['type'] = 'field';

        parent::__construct($data);
    }

    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return array_merge(parent::prototype(), [
            'field' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
        ]);
    }

    /**
     * Custom defaults to be made in object construct process
     *
     * @param array $data
     * @return void
     */
    protected function customDefaults(array &$data)
    {
        if (!array_key_exists('field', $data) and array_key_exists('name', $data)) {
            $data['field'] = $data['name'];
        }

        parent::customDefaults($data);
    }
}
