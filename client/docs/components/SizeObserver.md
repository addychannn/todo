[🠈](../index.md)

## SizeObserver

Observe element size changes.

### Example

```html
...
<SizeObserver @resize="onElementResize" />
...
```

```js
<script>
  export default {
    data() {
      return {
          width: 0,
          height: 0,
      };
    },
    methods: {
        onElementResize(size){
            this.width = size.width;
            this.height = size.height;
        }
    }
  };
</script>
```

### Options

| Property | Type | Description | Default |
| -------- | ---- | ----------- | ------- |
| **none** |

### Events

| Event    | Description                         | Return Type       |
| -------- | ----------------------------------- | ----------------- |
| `resize` | Fires when the element size changes | `{width, height}` |
