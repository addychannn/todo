<template>
  <transition
    enter-from-class="opacity-0 blur-md"
    leave-to-class="opacity-0 blur-md"
    enter-active-class="transition duration-300"
    leave-active-class="transition duration-700"
  >
    <div
      v-if="active"
      class="pointer-events-none fixed inset-0 z-[99999] select-none bg-negative/5"
    >
      <div
        v-for="position in positions"
        :key="position"
        class="absolute select-none rounded-lg border border-negative bg-light text-negative shadow-md shadow-negative/25"
        :class="[position]"
        @click="close"
      >
        <div class="animate-shake bg-negative/25 px-3 py-5 font-bold">
          <slot>
            <div class="flex items-center justify-center gap-1">
              <template v-if="!!message">
                <span class="relative leading-tight">
                  <span
                    class="absolute left-1/2 top-1/2 z-0 -translate-x-1/2 -translate-y-1/2 rounded-full bg-black p-2"
                  >
                  </span>
                  <TIcon
                    :name="icon"
                    size="lg"
                    class="z-10 text-warning drop-shadow"
                  />
                </span>
                <div>{{ message }}</div>
              </template>

              <div v-else>
                <pre class="text-xs">
                                  ....
                                .'' .'''
.                             .'   :
\\                          .:    :
 \\                        _:    :       ..----.._
  \\                    .:::.....:::.. .'         ''.
   \\                 .'  #-. .-######'     #        '.
    \\                 '.##'/ ' ################       :
     \\                  #####################         :
      \\               ..##.-.#### .''''###'.._        :
       \\             :--:########:            '.    .' :
        \\..__...--.. :--:#######.'   '.         '.     :
         :     :  : : '':'-:'':'::        .         '.  .'
         '---'''..: :    ':    '..'''.      '.        :'
           \\  :: : :     '      ''''''.     '.      .:
            \\ ::  : :     '            '.      '      :
             \\::   : :           ....' ..:       '     '.
              \\::  : :    .....####\\ .~~.:.             :
               \\':.:.:.:'#########.===. ~ |.'-.   . '''.. :
                \\    .'  ########## \ \ _.' '. '-.       '''.
                :\\  :     ########   \ \      '.  '-.        :
               :  \\'    '   #### :    \ \      :.    '-.      :
              :  .'\\   :'  :     :     \ \       :      '-.    :
             : .'  .\\  '  :      :     :\ \       :        '.   :
             ::   :  \\'  :.      :     : \ \      :          '. :
             ::. :    \\  : :      :    ;  \ \     :           '.:
              : ':    '\\ :  :     :     :  \:\     :        ..'
                 :    ' \\ :        :     ;  \|      :   .'''
                 '.   '  \\:                         :.''
                  .:..... \\:       :            ..''
                 '._____|'.\\......'''''''.:..'''
                            \\
              </pre
                >
                <div class="text-center text-3xl font-bold">
                  YOU SHALL NOT PASS!
                </div>
              </div>
            </div>
          </slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { inject, ref, watch } from "vue";
const devtAttempts = inject("devtAttempts");

const props = defineProps({
  message: {
    type: String,
    default: null,
  },
  icon: {
    type: String,
    default: "warning",
  },
  positions: {
    type: Object,
    default: () => [
      // "left-5 top-5",
      // "top-5 left-1/2 -translate-x-1/2",
      // "right-5 top-5",
      // "left-5 top-1/2 -translate-y-1/2",
      "left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2",
      // "right-5 top-1/2 -translate-y-1/2",
      // "left-5 bottom-5",
      // "bottom-5 left-1/2 -translate-x-1/2",
      // "right-5 bottom-5",
    ],
  },
});

const active = ref(false);
const timer = ref(null);

const close = () => {
  active.value = false;
  devtAttempts.value = 0;
  clearTimeout(timer.value);
};
watch(devtAttempts, (val) => {
  if (val > 0) {
    active.value = true;
    clearTimeout(timer.value);
    timer.value = setTimeout(() => {
      close();
    }, 3000);
  }
});
</script>
