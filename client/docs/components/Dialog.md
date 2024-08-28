[🠈](../index.md)

# Dialog

An alternative to the Bootstraps' Modal component.

```html
<Dialog v-model="myDialog">...</Dialog>
```

## Properties

| Property       | Type    | Description                                                                                                    | Default  |
| -------------- | ------- | -------------------------------------------------------------------------------------------------------------- | -------- |
| `v-model`      | Boolean | Whether the dialog is open                                                                                     | `false`  |
| `contentClass` | String  | Class to apply to the dialog content wrapper                                                                   | `""`     |
| `position`     | String  | Position of the dialog                                                                                         | `center` |
| `persistent`   | Boolean | When true, dialog will not close when backdrop is clicked                                                      | `false`  |
| `seamless`     | Boolean | When true, dialog will not have a backdrop                                                                     | `false`  |
| `transition`   | String  | Name of the transition to use                                                                                  | `bounce` |
| `contain`      | Boolean | When true, dialog will be contained in its' parent element                                                     | `false`  |
| `blur`         | Boolean | When true, dialog will blur the background when open (NOTICE!: Can decrease performance on lower end devices ) | `false`  |

## `Positions`

```js
    {
        "center",
        "left",
        "right",
        "top",
        "bottom",

        "top-left",
        "top-right",
        "bottom-left",
        "bottom-right",
    }
```

## `Transitions`

```js
    {
        "bounce",
        "slide-left",
        "slide-right",
        "slide-up",
        "slide-down",
        "bounce-slide-left",
        "bounce-slide-right",
        "bounce-slide-up",
        "bounce-slide-down",
    }
```

### Custom Transitions

You can add your own transistion/animation by modifying the `dialog.scss` in the `"src/css/transitions"` folder.

- You will have to add your _`-enter-active`_ and _`-leave-active`_ classes

```css
.__dialog__backdrop.{{NAME OF YOUR TRANSITION}}-enter-active .__dialog__content {
  ...
}

.__dialog__backdrop.{{NAME OF YOUR TRANSITION}}-leave-active .__dialog__content {
  ...
}

ex. Bounce transition

.__dialog__backdrop.bounce-enter-active .__dialog__content {
    animation: bounce 0.5s;
}

.__dialog__backdrop.bounce-leave-active .__dialog__content {
    animation: bounce 0.5s reverse;
}

```

You can get all (or add your own) custom animations in the `"animations.scss"` in the `"src/css/"` folder.
