<template>
  <Error
    :code="401"
    error="Unauthorized!"
    description="You are not authorized to view the requested page/data!"
  />
</template>

<script setup>
import Error from "./error.vue";
</script>
