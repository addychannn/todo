<template>
    <input
      :type="type"
      :value="value"
      :placeholder="placeholder"
      :class="inputClasses"
      @input="onInput"
      @focus="onFocus"
      @blur="onBlur"
    />
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue';
  
  // Define props
  const props = defineProps({
    type: {
      type: String,
      default: 'text'
    },
    value: {
      type: [String, Number],
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    },
    className: {
      type: String,
      default: ''
    }
  });
  
  // Define emits
  const emit = defineEmits(['update:value', 'focus', 'blur']);
  
  // Define computed class for the input
  const inputClasses = computed(() => {
    return `block w-full p-2 text-gray-900 border border-gray-300 rounded-md bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ${props.className}`;
  });
  
  // Handle input event
  const onInput = (event) => {
    emit('update:value', event.target.value);
  };
  
  // Handle focus event
  const onFocus = (event) => {
    emit('focus', event);
  };
  
  // Handle blur event
  const onBlur = (event) => {
    emit('blur', event);
  };
  </script>
  
  <style scoped>
  /* Add additional styling if needed */
  </style>
  