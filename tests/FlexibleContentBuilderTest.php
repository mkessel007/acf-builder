<?php

namespace StoutLogic\AcfBuilder\Tests;

use StoutLogic\AcfBuilder\FlexibleContentBuilder;

class FlexibleContentBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildFlexibleContent()
    {
        $builder = new FlexibleContentBuilder('content_areas');
        $builder->addLayout('banner')
                    ->addText('title')
                    ->addWysiwyg('content')
                ->addLayout('content_columns')
                    ->addRepeater('columns', ['min' => 1, 'max' => 2])
                        ->addWysiwyg('content');

        $expectedConfig =  [
            'key' => 'content_areas',
            'name' => 'content_areas',
            'label' => 'Content Areas',
            'type' => 'flexible_content',
            'button' => 'Add Content Area',
            'layouts' => [
                [
                    'key' => 'field_content_areas_banner',
                    'name' => 'banner',
                    'label' => 'Banner',
                    'display' => 'block',
                    'sub_fields' => [
                        [
                            'key' => 'field_content_areas_banner_title',
                            'name' => 'title',
                            'type' => 'text',
                        ],
                        [
                            'key' => 'field_content_areas_banner_content',
                            'name' => 'content',
                            'type' => 'wysiwyg',
                        ]
                    ]
                ],
                [
                    'key' => 'field_content_areas_content_columns',
                    'name' => 'content_columns',
                    'label' => 'Content Columns',
                    'display' => 'block',
                    'sub_fields' => [
                        [
                            'key' => 'field_content_areas_content_columns_columns',
                            'name' => 'columns',
                            'type' => 'repeater',
                            'min' => 1,
                            'max' => 2,
                            'sub_fields' => [
                                [
                                    'key' => 'field_content_areas_content_columns_columns_content',
                                    'name' => 'content',
                                    'type' => 'wysiwyg',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertArraySubset($expectedConfig, $builder->build());
        $this->assertArrayNotHasKey('fields', $builder->build());
    }

    function testAddingFieldsBuilderAsLayout()
    {
        $banner = $this->getMockBuilder('StoutLogic\AcfBuilder\FieldsBuilder')
                        ->setConstructorArgs(['parent'])
                        ->getMock();

        $banner->expects($this->once())->method('build')->willReturn([
            'key' => 'group_banner',
            'name' => 'banner',
            'title' => 'Banner',
            'display' => 'block',
            'fields' => [
                [
                    'key' => 'field_banner_title',
                    'name' => 'title',
                    'type' => 'text',
                ],
                [
                    'name' => 'content',
                    'type' => 'wysiwyg',
                ]
            ]
        ]);

        $builder = new FlexibleContentBuilder('content_areas');
        $builder->addLayout($banner);

        $expectedConfig =  [
            'key' => 'content_areas',
            'name' => 'content_areas',
            'label' => 'Content Areas',
            'type' => 'flexible_content',
            'button' => 'Add Content Area',
            'layouts' => [
                [
                    'key' => 'field_content_areas_banner',
                    'name' => 'banner',
                    'label' => 'Banner',
                    'display' => 'block',
                    'sub_fields' => [
                        [
                            'key' => 'field_content_areas_banner_title',
                            'name' => 'title',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'content',
                            'type' => 'wysiwyg',
                        ]
                    ]
                ],
            ]
        ];

        $config = $builder->build();
        $this->assertArraySubset($expectedConfig, $config);
    }

    public function testEndFlexibleContent()
    {
        $fieldsBuilder = $this->getMockBuilder('StoutLogic\AcfBuilder\FieldsBuilder')
                              ->setConstructorArgs(['parent'])
                              ->getMock();

        $builder = new FlexibleContentBuilder('content_areas');
        $builder->setParentContext($fieldsBuilder);
        $fieldsBuilder->expects($this->once())->method('addText');

        $builder
            ->addLayout('banner')
                ->addText('title')
                ->addWysiwyg('content')
            ->addLayout('content_columns')
                ->addRepeater('columns', ['min' => 1, 'max' => 2])
                    ->addWysiwyg('content')
            ->endFlexibleContent()
        ->addText('parent_title');
    }

    public function testSetLocation()
    {
        $fieldsBuilder = $this->getMockBuilder('StoutLogic\AcfBuilder\FieldsBuilder')
                              ->setConstructorArgs(['parent'])
                              ->getMock();

        $builder = new FlexibleContentBuilder('content_areas');
        $builder->setParentContext($fieldsBuilder);
        $fieldsBuilder->expects($this->once())->method('setLocation');

        $builder
            ->addLayout('banner')
                ->addText('title')
                ->addWysiwyg('content')
            ->addLayout('content_columns')
                ->addRepeater('columns', ['min' => 1, 'max' => 2])
                    ->addWysiwyg('content')
            ->setLocation('post_type', '==', 'page');
    }
}
