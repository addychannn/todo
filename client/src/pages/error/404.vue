<template>
  <Error
    :code="404"
    error="Not Found!"
    description="The page you requested does not exist!"
  />
</template>

<script setup>
import Error from "./error.vue";
</script>
