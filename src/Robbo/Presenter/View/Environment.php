<?php namespace Robbo\Presenter\View;

use ArrayAccess;
use IteratorAggregate;
use Robbo\Presenter\PresentableInterface;
use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment {

	/**
	 * Get a evaluated view contents for the given view.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return Illuminate\View\View
	 */
	public function make($view, $data = array())
	{
		$path = $this->finder->find($view);

		return new View($this, $this->getEngineFromPath($path), $view, $path, $this->recurseMakePresentable($data));
	}

	/**
	 * Add a piece of shared data to the environment.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function share($key, $value)
	{
		return parent::share($key, $this->makePresentable($value));
	}

	/**
	 * If this variable implements Robbo\Presenter\PresentableInterface then turn it into a presenter.
	 *
	 * @param  mixed $value
	 * @return mixed $value
	*/
	public function makePresentable($value)
	{
		return $value instanceof PresentableInterface ? $value->getPresenter() : $value;
	}

	/*
	 * Recurse through arrays and objects making anything Presentable into Presenters
	 *
	 * @param  array $data
	 * @return array $data
	 */
	protected function recurseMakePresentable($data)
	{
		foreach ($data AS $key => $value)
		{
			if (is_array($value) OR ($value instanceof IteratorAggregate AND $value instanceof ArrayAccess))
			{
				$data[$key] = $this->recurseMakePresentable($value);
			}
			else
			{
				$data[$key] = $this->makePresentable($value);
			}
		}

		return $data;
	}
}