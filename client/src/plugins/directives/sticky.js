import StickyBits from "stickybits";
import { Helpers } from "@/scripts";

class stickItToMe {
  defaults = {
    useStickyClasses: true,
    scrollEl: "body",
    useFixed: true,
    zIndex: 2,
    disabled: false,
  };

  styles = {
    parent: {
      paddingTop: "",
    },
    el: {},
    sibling: {
      marginBottom: "",
    },
  };
  constructor(el, binding) {
    this.options = Object.assign({}, this.defaults, binding);

    this.element = el;

    this.styles.parent.paddingTop = this._getComputedStyle(
      el.parentElement,
      "padding-top"
    );

    this.update();
  }

  update = () => {
    if (this.options.disabled) {
      this.cleanUp();
    } else {
      this.attachObservers(this.element);
      // if (!!this.stickybits && this.stickybits.instances.length > 0) {
      //   this.stickybits.update();
      // } else {
      // }
      this.stickybits = StickyBits(this.element, this.options);
      this.element.style.top = "";
    }
  };

  attachObservers = (el) => {
    this.mo = new MutationObserver(this.onAttribChange);
    this.mo.observe(el, { attributes: true });

    this.ro = new ResizeObserver(this.onParentResize);
    this.ro.observe(el.parentElement);
  };

  onAttribChange = (mutationList, observer) => {
    let styles = this.styles;
    mutationList.forEach(function (mutation) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "class"
      ) {
        if (!!mutation.target.previousElementSibling) {
          if (
            Object.values(mutation.target.classList).includes("js-is-sticky")
          ) {
            mutation.target.previousElementSibling.style.marginBottom = `${
              mutation.target.offsetHeight +
              (styles.sibling.marginBottom.replace(/\D/g, "") ?? 0)
            }px`;
          } else {
            mutation.target.previousElementSibling.style.marginBottom =
              styles.sibling.marginBottom;
          }
        } else {
          let _parent = mutation.target.parentElement;
          if (
            Object.values(mutation.target.classList).some((element) => {
              return element == "js-is-sticky" || element == "js-is-stuck";
            })
          ) {
            let paddingTop = `${mutation.target.clientHeight}px`;
            _parent ? (_parent.style.paddingTop = paddingTop) : null;
          } else {
            _parent
              ? (_parent.style.paddingTop = styles.parent.paddingTop)
              : null;
          }
        }
        if (Object.values(mutation.target.classList).includes("js-is-stuck")) {
          mutation.target.parentElement.style.position = "relative";
          mutation.target.style.position = "absolute";
        }
      }
    });
  };

  onParentResize = (entries) => {
    // if (!!this.element.previousElementSibling) {
    //   this.styles.sibling.marginBottom =
    //     this.element.previousElementSibling.style.marginBottom;
    // }
    if (!this.options.disabled) {
      this.element.style.width = `${this.element.parentElement.clientWidth}px`;
      this.element.style.zIndex = this.options.zIndex;
      this.stickybits?.update();
    }
  };

  cleanUp = () => {
    if (!!this.stickybits) {
      this.stickybits.cleanup();
    }
    this.mo?.disconnect();
    this.ro?.unobserve(this.element.parentElement);

    this.element.style.width = "";
    this.element.style.zIndex = "";
    this.element.style.position = "";

    let parent = this.element.parentElement;
    parent.style.paddingTop = "";
    parent.style.position = "";
  };

  _getComputedStyle = (el, prop) => {
    return window.getComputedStyle(el).getPropertyValue(prop);
  };
}

let instances = {};

const sticky = {
  mounted: (el, binding) => {
    el.dataset.uid = el.dataset.uid ?? Helpers.uniqid("sticky");
    instances = Object.assign({}, instances, {
      [el.dataset.uid]: new stickItToMe(el, binding.value),
    });
  },
  updated: (el, binding) => {
    instances[el.dataset.uid].options = Object.assign(
      {},
      instances[el.dataset.uid].options,
      binding.value
    );
    instances[el.dataset.uid].update();
  },
  beforeUnmount: (el, binding) => {
    instances[el.dataset.uid].cleanUp();
    instances[el.dataset.uid] = null;
  },
};

export default sticky;
