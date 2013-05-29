<?php
namespace AtDataGrid\DataGrid\Column\Decorator;
use Nette\Diagnostics\Debugger;
use Doctrine\ORM\PersistentCollection;
use Zend\View\Model\ViewModel;
use AtDataGrid\DataGrid\Column\Column;

class DbReference extends AbstractDecorator
{

	/**
	 *
	 * @var \AtDataGrid\DataGrid\DataSource\DoctrineDbTableGateway
	 */
	protected $dataSource = null;
	
	/**
	 *
	 * @param \Zend\Db\TableGateway\TableGateway $tableGateway        	
	 * @param
	 *        	$referenceField
	 * @param
	 *        	$resultFieldName
	 */
	public function __construct ($dataSource,  Column $column)
	{
		$this->dataSource = $dataSource;
		parent::__construct($column);
	}

	/**
	 *
	 * @param
	 *        	$value
	 * @param
	 *        	$row
	 * @return
	 *
	 */
	public function render ($value)
	{
		if (! $value) {
			return '';
		}
		
		$containsColumns = false;
		$columns = $this->column->getColumns();
		foreach((array) $columns as $column) {
			if($column->isVisible()) {
				$containsColumns = true;
				break;
			}
		}
		
		$allEntities = $this->dataSource->getEm()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
		
		if($containsColumns) {
			$models = array();
			foreach($columns as $column) {
				if($column->isVisible()) {
					$models[] = $column->render($value->{"get{$column->getName()}"}());
				}
			}
			
			return $models;
			
		} else {
			switch (true) {
				case $value instanceof PersistentCollection:
					$value = $value->count();
					break;
				case  in_array((string) get_parent_class($value), $allEntities) !== false:
					$value = $value->getId();
					break;
				default:
					break;
			}
		}
		
		return parent::render($value);
		
	}
}