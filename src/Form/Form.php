<?php
namespace CryCMS\Form;

use CryCMS\CRUDHelper;
use CryCMS\Form\Interfaces\FormInterface;
use CryCMS\Exceptions\ThingValidateException;
use CryCMS\HTML;
use CryCMS\Thing;
use RuntimeException;

class Form
{
    protected string $class;
    protected array $classPresetProperies = [];
    protected FormInterface|null $instance;

    public string $pageBase = '/';

    public const FIELD_TEXT = 'input-text';
    public const FIELD_TEXTAREA = 'textarea';
    public const FIELD_NUMBER = 'input-number';
    public const FIELD_CHECKBOX = 'checkbox';
    public const FIELD_CHECKBOX_LIST = 'checkbox-list';
    public const FIELD_SELECT = 'select';
    public const FIELD_DATE = 'input-date';
    public const FIELD_TIME = 'input-time';
    public const FIELD_WYSIWYG = 'wysiwyg';
    public const FIELD_IMAGES_LIST = 'images-list';

    protected const FIELD_METHODS = [
        self::FIELD_TEXT => 'InputText',
        self::FIELD_TEXTAREA => 'TextArea',
        self::FIELD_NUMBER => 'InputNumber',
        self::FIELD_CHECKBOX => 'CheckBox',
        self::FIELD_CHECKBOX_LIST => 'CheckBoxList',
        self::FIELD_SELECT => 'Select',
        self::FIELD_DATE => 'InputDate',
        self::FIELD_TIME => 'InputTime',
        self::FIELD_WYSIWYG => 'Wysiwyg',
        self::FIELD_IMAGES_LIST => 'ImagesList',
    ];

    public function __construct(string $class, array $classPresetProperies = [])
    {
        if (!class_exists($class)) {
            throw new RuntimeException('Class is not exists');
        }

        $this->class = $class;
        $this->classPresetProperies = $classPresetProperies;
    }

    public function onlyForm($request): void
    {
        $this->instance = new $this->class();

        if (!empty($this->classPresetProperies)) {
            $this->instance->setAttributes($this->classPresetProperies);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->instance->setAttributes($request);

            try {
                $this->instance->validate();
            } catch (ThingValidateException $e) {
                $this->instance->addError('form', $e->getMessage());
            }
        }

        echo $this->template('form', [
            'form' => $this,
        ]);
    }

    /** @noinspection PhpUnused */
    public function defaultActionCreate($request): void
    {
        $this->instance = new $this->class();

        if (!empty($this->classPresetProperies)) {
            $this->instance->setAttributes($this->classPresetProperies);
        }

        $this->defaultActionCU($request);
    }

    /** @noinspection PhpUnused */
    public function defaultActionUpdate($actionId, $request): void
    {
        /** @var Thing $class */
        $class = $this->class;
        $this->instance = $class::find()->byPk($actionId);
        if ($this->instance === null) {
            CRUDHelper::redirect($this->pageBase);
        }

        if (!empty($this->classPresetProperies)) {
            $this->instance->setAttributes($this->classPresetProperies);
        }

        $this->defaultActionCU($request);
    }

    protected function defaultActionCU($request): void
    {
        if (method_exists($this->instance, 'beforeFormShow')) {
            $this->instance->beforeFormShow();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->instance->setAttributes($request);

            try {
                $this->instance->save();
                CRUDHelper::redirect($this->pageBase);
            } catch (ThingValidateException $e) {
                $this->instance->addError('form', $e->getMessage());
            }
        }

        echo $this->template('form', [
            'form' => $this,
        ]);
    }

    /** @noinspection PhpUnused */
    public function defaultActionDelete($actionId): void
    {
        /** @var Thing $class */
        $class = $this->class;
        $this->instance = $class::find()->byPk($actionId);
        if ($this->instance === null) {
            CRUDHelper::redirect($this->pageBase);
        }

        if (!empty($this->classPresetProperies)) {
            $this->instance->setAttributes($this->classPresetProperies);
        }

        if (method_exists($this->instance, 'beforeFormDelete')) {
            $this->instance->beforeFormDelete();
        }

        try {
            $this->instance->delete();
        }
        catch (RuntimeException) {

        }

        CRUDHelper::redirect($this->pageBase);
    }

    /** @noinspection PhpUnused */
    public function defaultActionRestore($actionId): void
    {
        $this->instance = $this->instance::find()->byPk($actionId);
        if ($this->instance === null) {
            CRUDHelper::redirect($this->pageBase);
        }

        if (!empty($this->classPresetProperies)) {
            $this->instance->setAttributes($this->classPresetProperies);
        }

        $this->instance->setAttribute('deleted', 0);

        try {
            $this->instance->save();
        } catch (ThingValidateException) {

        }

        CRUDHelper::redirect($this->pageBase);
    }

    public function generateFields(): void
    {
        $fields = $this->instance->getFieldsList();

        foreach ($fields as $field => $properties) {
            if (array_key_exists($properties['type'], static::FIELD_METHODS)) {
                $method = 'generateField' . static::FIELD_METHODS[$properties['type']];
                if (method_exists($this, $method)) {
                    $this->$method($field, $properties);
                }
            }
        }

        if (!empty($this->instance->getError('form'))) {
            echo HTML::div($this->instance->getError('form'), ['class' => 'alert alert-danger', 'role' => 'alert']);
        }
    }

    public function template(string $type, array $params = []): string
    {
        extract($params, EXTR_SKIP);

        $path = __DIR__ . '/Templates/' . $type . '.tpl.php';
        if (file_exists($path)) {
            ob_start();
            include $path;
            return ob_get_clean();
        }

        throw new RuntimeException('Template "' . $type . '" is not exists in Form component', 404);
    }

    public function getInstance(): ?FormInterface
    {
        return $this->instance;
    }

    /** @noinspection PhpUnused */
    protected function generateFieldInputText(string $field, array $properties): void
    {
        $fieldProperties = [
            'data' => $this->instance,
            'title' => $properties['title'],
            'field' => $field,
        ] + $properties;

        if (array_key_exists('type', $fieldProperties)) {
            unset($fieldProperties['type']);
        }

        $content = $this->template('form-input-text', $fieldProperties);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    /** @noinspection PhpUnused */
    protected function generateFieldTextArea(string $field, array $properties): void
    {
        $fieldProperties = [
            'data' => $this->instance,
            'title' => $properties['title'],
            'field' => $field,
        ];

        if (!empty($properties['rows'])) {
            $fieldProperties['rows'] = $properties['rows'];
        }

        $content = $this->template('form-textarea', $fieldProperties);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    /** @noinspection PhpUnused */
    protected function generateFieldInputNumber(string $field, array $properties): void
    {
        $content = $this->template('form-input-number', [
            'data' => $this->instance,
            'title' => $properties['title'],
            'field' => $field,
            'readonly' => $properties['readonly'] ?? false,
            'min' => $properties['min'] ?? 1,
            'max' => $properties['max'] ?? 100500,
            'step' => $properties['step'] ?? 1,
        ]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    /** @noinspection PhpUnused */
    protected function generateFieldCheckBox(string $field, array $properties): void
    {
        if (array_key_exists('checked', $properties)) {
            $checked = $properties['checked'];
        }
        else {
            $checked = !empty($this->instance->$field);
        }

        $content = $this->template('form-checkbox', [
            'title' => $properties['title'],
            'name' => $field,
            'value' => '1',
            'checked' => $checked,
            'tooltip' => '',
        ]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    /** @noinspection PhpUnused */
    protected function generateFieldCheckBoxList(string $field, array $properties): void
    {
        if (empty($properties['list'])) {
            return;
        }

        if (mb_strpos($properties['list'], '::', 0, 'UTF-8') === false) {
            return;
        }

        [$listClass, $listMethod] = explode('::', $properties['list']);
        $list = $listClass::{$listMethod}();

        $content = $this->template('form-checkbox-list', [
            'data' => $this->instance,
            'title' => $properties['title'],
            'field' => $field,
            'addEmpty' => true,
            'list' => $list,
            'checked' => $properties['checked'],
        ]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    /** @noinspection PhpUnused */
    protected function generateFieldSelect(string $field, array $properties): void
    {
        if (empty($properties['list'])) {
            return;
        }

        $list = null;

        if (is_array($properties['list'])) {
            $list = $properties['list'];
        }
        elseif (mb_strpos($properties['list'], '::', 0, 'UTF-8') !== false) {
            [$listClass, $listMethod] = explode('::', $properties['list']);
            $list = $listClass::{$listMethod}();
        }

        if ($list === null) {
            return;
        }

        $content = $this->template('form-select', [
            'data' => $this->instance,
            'title' => $properties['title'],
            'field' => $field,
            'empty' => $properties['empty'] ?? false,
            'list' => $list,
        ]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    /** @noinspection PhpUnused */
    protected function generateFieldInputDate(string $field, array $properties): void
    {
        $content = $this->template('form-input-date', ['data' => $this->instance, 'title' => $properties['title'], 'field' => $field]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    /** @noinspection PhpUnused */
    protected function generateFieldInputTime(string $field, array $properties): void
    {
        $content = $this->template('form-input-time', ['data' => $this->instance, 'title' => $properties['title'], 'field' => $field]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    protected function generateFieldWysiwyg(string $field, array $properties): void
    {
        $content = $this->template('form-wysiwyg', ['data' => $this->instance, 'title' => $properties['title'], 'field' => $field]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }

    protected function generateFieldImagesList(string $field, array $properties): void
    {
        $content = $this->template('form-images-list', [
            'data' => $this->instance,
            'title' => $properties['title'],
            'field' => $field,
            'list' => $properties['list'] ?? [],
        ]);

        echo HTML::div($content, ['class' => 'mb-3']);
    }
}