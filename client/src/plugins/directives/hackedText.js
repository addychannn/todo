import { Helpers } from "@/scripts";

const upperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
const lowerCase = "abcdefghijklmnopqrstuvwxyz";
const numbers = "0123456789";

class hackText {
  defaults = {
    iterations: 3,
    speed: 30,
    upperCase: true,
    lowerCase: true,
    numbers: true,
    chars: null,
    event: "mouseover",
  };

  events = [
    "mouseover",
    "mouseout",
    "mousedown",
    "mouseup",
    "click",
    "focus",
    "blur",
    "change",
    "reset",
    "submit",
    "paste",
    "cut",
    "copy",
  ];

  interval = null;
  el = null;

  constructor(el, options) {
    if (typeof options == "string") options = { text: options };
    if (!options || !options.text) {
      options = Object.assign({}, options, {
        text: el.innerText,
      });
    }

    this.options = Object.assign({}, this.defaults, options);
    this.options.event =
      this.events.indexOf(options?.event) > -1 ? options.event : "mouseover";
    this.el = el;
    this.el.addEventListener(this.options.event, this.onEvent);
  }

  onEvent = (e) => {
    let text = !!this.options.text ? this.options.text : e.target.innerText;
    if (!!text) {
      let iteration = 0;
      let pass = 0;

      let chars = this.getChars(this.options);

      clearInterval(this.interval);
      this.interval = setInterval(() => {
        e.target.innerText = text
          .split("")
          .map((char, index) => {
            if (index < iteration) {
              return text[index];
            }
            return chars[Math.floor(Math.random() * chars.length)];
          })
          .join("");

        iteration += 1 / (this.options.iterations ?? 3);
        if (pass > text.length * (this.options.iterations ?? 3)) {
          clearInterval(this.interval);
        }
        pass++;
      }, this.options.speed);
    }
  };

  getChars = (options) => {
    if (!!options.chars && options.chars.length > 0) {
      return options.chars;
    }
    let chars = "";

    if (options.upperCase) {
      chars += upperCase;
    }

    if (options.lowerCase) {
      chars += lowerCase;
    }

    if (options.numbers) {
      chars += numbers;
    }

    return chars;
  };

  cleanUp = () => {
    this.el.removeEventListener("mouseover", () => this.onEvent);
  };
}

let instances = {};

const hackedText = {
  mounted: (el, binding) => {
    el.dataset.uid = el.dataset.uid ?? Helpers.uniqid("hacked");
    instances = Object.assign({}, instances, {
      [el.dataset.uid]: new hackText(el, binding.value),
    });
  },
  beforeUnmount: (el, binding) => {
    instances[el.dataset.uid].cleanUp();
    instances[el.dataset.uid] = null;
  },
};

export default hackedText;
