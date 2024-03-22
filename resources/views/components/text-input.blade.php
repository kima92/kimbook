@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-input rounded-md shadow-sm border-gray-300 dark:text-white dark:bg-black dark:border-white dark:focus:border-white  focus:border-indigo-500 focus:ring-indigo-500 shadow-sm']) !!}>
