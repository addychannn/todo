[🠈](../index.md)

## Pagination

Paginate data using MDB vue pagination component.

### Example

```html
...
<Pagination
  v-model="pagination.page"
  v-model:limit="pagination.limit"
  @update:offset="
    (offset) => {
      pagination.offset = offset;
    }
  "
  @update:totalPage="
    (pages) => {
      pagination.pages = pages;
    }
  "
  @paginate="getData"
  :total="pagination.total"
  direction-nav
  boundary-nav
  hide-limit-select
  :limit-options="[10, 20, 30, 40, 50, 100]"
/>
...
```

```js
<script>
  export default {
    data() {
      return {
        pagination:{
          page: 1,
          pages: 1, // Total computed page count
          total: 0,
          limit: 10,
          offset: 0,
        }
      };
    },
    methods: {
        getData(){
            ...
        }
    }
  };
</script>
```

### Options

| Property            | Type    | Description                                               | Default                |
| ------------------- | ------- | --------------------------------------------------------- | ---------------------- |
| `v-model`           | Number  | Current page number                                       | **REQUIRED**           |
| `total`             | Number  | Total number of records from the database                 | **REQUIRED**           |
| `limit`             | Number  | Number of items per page                                  | `10`                   |
| `maxPages`          | Number  | Number of pages to show in the pagination                 | `5`                    |
| `boundary-nav`      | Boolean | Show/hide the pagination boundary links (First/Last page) | `false`                |
| `direction-nav`     | Boolean | Show/hide the pagination direction links (Previous/Next)  | `true`                 |
| `hide-page-text`    | Boolean | Show/hide the pagination text                             | `false`                |
| `hide-limit-select` | Boolean | Show/hide the pagination limit select                     | `false`                |
| `limit-options`     | Array   | Array of numbers to show in the pagination limit select   | `[5, 10, 25, 50, 100]` |

## Events

| Event                | Description                                      | Return Type |
| -------------------- | ------------------------------------------------ | ----------- |
| `update:model-value` | Emitted when the page number is changed          | `Number`    |
| `update:offset`      | Emitted when the page offset is calculated       | `Number`    |
| `update:limit`       | Emitted when the page limit is changed           | `Number`    |
| `update:totalPage`   | Emitted when the total page count is calculated  | `Number`    |
| `paginate`           | Emitted when the Page number or limit is changed | **None**    |
