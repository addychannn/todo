[🠈](../index.md)

# ImageSelect

Select image from local storage.

```html
<ImageSelect
  v-model="image"
  :initial-image="{your initial image url}"
  class="border border-5"
  circle
/>
```

## Properties

| Property       | Type    | Description                                                                                                                                                     | Default      |
| -------------- | ------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------ |
| `v-model`      | Object  | Selected image file                                                                                                                                             | **REQUIRED** |
| `initialImage` | String  | Initial image url, this will be shown if no image is selected                                                                                                   | `null`       |
| `circle`       | Boolean | Whether to preview the image in a circular border                                                                                                               | `false`      |
| `size`         | Number  | Width of the image in px. Height will be computed based ratio                                                                                                   | `200`        |
| `ratio`        | String  | Ratio of the image, used to compute the height of the image. Refer to [MDBVue's ratios](https://mdbootstrap.com/docs/vue/utilities/ratio#section-aspect-ratios) | `1x1`        |
