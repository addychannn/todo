import { defineAsyncComponent } from "vue";
// prettier-ignore
export default {
  TInnerLoading: defineAsyncComponent(() => import("./tInnerLoading.vue")),
  TIcon: defineAsyncComponent(() => import("./tIcon.vue")),
  TFileIcon: defineAsyncComponent(() => import("./tFileIcon.vue")),
  TButton: defineAsyncComponent(() => import("./tButton.vue")),
  TCollapse: defineAsyncComponent(() => import("./tCollapse.vue")),
  TImage: defineAsyncComponent(() => import("./tImage.vue")),
  TToolTip: defineAsyncComponent(() => import("./tToolTip.vue")),
  TMenu: defineAsyncComponent(() => import("./tMenu.vue")),
  TPagination: defineAsyncComponent(() => import("./tPagination.vue")),
  TTabProgress: defineAsyncComponent(() => import("./tTabProgress.vue")),

  // Layout
  Layout: defineAsyncComponent(() => import("./layout/layout.vue")),
  Header: defineAsyncComponent(() => import("./layout/header.vue")),
  Footer: defineAsyncComponent(() => import("./layout/footer.vue")),
  PageContainer: defineAsyncComponent(() =>
    import("./layout/pageContainer.vue")
  ),
  Page: defineAsyncComponent(() => import("./layout/page.vue")),
  SideBar: defineAsyncComponent(() => import("./layout/sideBar.vue")),
  // End - Layout

  // Card
  TCard: defineAsyncComponent(() => import("./card/tCard.vue")),
  TCardHeader: defineAsyncComponent(() => import("./card/tCardHeader.vue")),
  TCardBody: defineAsyncComponent(() => import("./card/tCardBody.vue")),
  TCardFooter: defineAsyncComponent(() => import("./card/tCardFooter.vue")),
  TCardTitle: defineAsyncComponent(() => import("./card/tCardTitle.vue")),
  TDialog: defineAsyncComponent(() => import("./tDialog.vue")),
  // End - Card

  // Forms
  TInput: defineAsyncComponent(() => import("./forms/tInput.vue")),
  TFieldWrapper: defineAsyncComponent(() =>
    import("./forms/tFieldWrapper2.vue")
  ),
  TTextArea: defineAsyncComponent(() => import("./forms/tTextArea.vue")),
  TCheckBox: defineAsyncComponent(() => import("./forms/tCheckBox.vue")),
  TRadio: defineAsyncComponent(() => import("./forms/tRadio.vue")),
  TList: defineAsyncComponent(() => import("./forms/tList.vue")),
  TSelect: defineAsyncComponent(() => import("./forms/tSelect.vue")),
  TImageSelect: defineAsyncComponent(() => import("./forms/tImageSelect.vue")),
  TDate: defineAsyncComponent(() => import("./forms/tDate.vue")),
  TSimpleDate: defineAsyncComponent(() => import("./forms/tSimpleDate.vue")),
  TFileDrop: defineAsyncComponent(() => import("./forms/tFileDrop.vue")),
  TFileInfo: defineAsyncComponent(() =>
    import("./forms/tFileDrop/fileInfo.vue")
  ),
  TToggle: defineAsyncComponent(() => import("./forms/tToggle.vue")),
  TColorPicker: defineAsyncComponent(() => import("./others/tColorPicker.vue")),
  TTextEditor: defineAsyncComponent(() =>
    import("./forms/textEditor/index.vue")
  ),
  TFormWizard: defineAsyncComponent(() => import("./forms/wizard/index.vue")),
  TFormWizardTab: defineAsyncComponent(() => import("./forms/wizard/tab.vue")),
  // End - Forms

  // Spinners
  TSpinnerOrbit: defineAsyncComponent(() => import("./spinners/orbit.vue")),
  TSpinnerCircles: defineAsyncComponent(() =>
    import("./spinners/spinningCircles.vue")
  ),
  // End - Spinners

  // Utils
  SizeObserver: defineAsyncComponent(() => import("./sizeObserver.vue")),
  // End - Utils

  // TProgress
  TProgress: defineAsyncComponent(() => import("./tProgress2.vue")),
  TProgressBar: defineAsyncComponent(() => import("./tProgressBar.vue")),
  TProgressCircle: defineAsyncComponent(() => import("./tProgressCircle.vue")),
  // End - TProgress

  // TTimeline
  TTimeline: defineAsyncComponent(() => import("./timeline/vertical.vue")),
  // End - TTimeline

  // Others
  TScrollUp: defineAsyncComponent(() => import("./tScrollUp.vue")),
  TGroup: defineAsyncComponent(() => import("./others/tGroup.vue")),
  TAddress: defineAsyncComponent(() => import("./others/tAddress.vue")),
  TSearcher: defineAsyncComponent(() => import("./others/tSearcher.vue")),
  TSearchButton: defineAsyncComponent(() =>
    import("./others/tSearchButton.vue")
  ),
  TPopover: defineAsyncComponent(() => import("./tPopover.vue")),
  TReadMore: defineAsyncComponent(() => import("./others/tReadMore.vue")),

  ThemeToggle: defineAsyncComponent(() => import("./others/themeToggle.vue")),
  FocusHelper: defineAsyncComponent(() => import("./others/focusHelper.vue")),
  ErrorTemplate: defineAsyncComponent(() =>
    import("./others/errorTemplate.vue")
  ),
  ScreenLocker: defineAsyncComponent(() => import("./others/screenLocker.vue")),
  PageResultCount: defineAsyncComponent(() =>
    import("./others/pageResultCount.vue")
  ),
  SearchableColumn: defineAsyncComponent(() =>
    import("./others/searchableColumn.vue")
  ),
  SelectionColumn: defineAsyncComponent(() =>
    import("./others/selectionColumn.vue")
  ),
  TSkeleton: defineAsyncComponent(() => import("./skeletons/index.vue")),
  TPin: defineAsyncComponent(() => import("./others/tPin.vue")),
  TPageLoader: defineAsyncComponent(() => import("./others/pageLoader.vue")),
  TCodeScan: defineAsyncComponent(() => import("./others/tCodeScan/index.vue")),
  TCamera: defineAsyncComponent(() => import("./others/tCamera/index.vue")),
  // End - Others

// Viewers
  TPdf: defineAsyncComponent(() => import("./viewers/pdf/index.vue")),
// End - Viewers
};
