<template>
  <Error
    :code="403"
    error="Forbidden!"
    description="You do not have permission to access the requested page/data!"
  />
</template>

<script setup>
import Error from "./error.vue";
</script>
