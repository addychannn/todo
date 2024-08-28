import TInnerLoading from "./tInnerLoading.vue";
import TIcon from "./tIcon.vue";
import TFileIcon from "./tFileIcon.vue";
import TButton from "./tButton.vue";
import TCollapse from "./tCollapse.vue";
import TImage from "./tImage.vue";
import TToolTip from "./tToolTip.vue";
import TMenu from "./tMenu.vue";
import TPagination from "./tPagination.vue";
import TTabProgress from "./tTabProgress.vue";

// Layout
import Layout from "./layout/layout.vue";
import Header from "./layout/header.vue";
import Footer from "./layout/footer.vue";
import PageContainer from "./layout/pageContainer.vue";
import Page from "./layout/page.vue";
import SideBar from "./layout/sideBar.vue";
// End - Layout

// Card
import TCard from "./card/tCard.vue";
import TCardHeader from "./card/tCardHeader.vue";
import TCardBody from "./card/tCardBody.vue";
import TCardFooter from "./card/tCardFooter.vue";
import TCardTitle from "./card/tCardTitle.vue";
import TDialog from "./tDialog.vue";
// End - Card

// Forms
import TInput from "./forms/tInput.vue";
import TFieldWrapper from "./forms/tFieldWrapper2.vue";
import TTextArea from "./forms/tTextArea.vue";
import TCheckBox from "./forms/tCheckBox.vue";
import TRadio from "./forms/tRadio.vue";
import TList from "./forms/tList.vue";
import TSelect from "./forms/tSelect.vue";
import TImageSelect from "./forms/tImageSelect.vue";
import TDate from "./forms/tDate.vue";
import TSimpleDate from "./forms/tSimpleDate.vue";
import TFileDrop from "./forms/tFileDrop.vue";
import TFileInfo from "./forms/tFileDrop/fileInfo.vue";
import TToggle from "./forms/tToggle.vue";
import TColorPicker from "./others/tColorPicker.vue";
import TTextEditor from "./forms/textEditor/index.vue";
import TFormWizard from "./forms/wizard/index.vue";
import TFormWizardTab from "./forms/wizard/tab.vue";
// End - Forms

// Spinners
import TSpinnerOrbit from "./spinners/orbit.vue";
import TSpinnerCircles from "./spinners/spinningCircles.vue";
// End - Spinners

// Utils
import SizeObserver from "./sizeObserver.vue";
// End - Utils

// Progress Bar
import TProgress from "./tProgress2.vue";
import TProgressBar from "./tProgressBar.vue";
import TProgressCircle from "./tProgressCircle.vue";
// End - Progress Bar

// Timeline
import TTimeline from "./timeline/vertical.vue";
// End - Timeline

// Others
import ThemeToggle from "./others/themeToggle.vue";
import FocusHelper from "./others/focusHelper.vue";
import TScrollUp from "./tScrollUp.vue";
import TGroup from "./others/tGroup.vue";
import TAddress from "./others/tAddress.vue";
import TSearcher from "./others/tSearcher.vue";
import TSearchButton from "./others/tSearchButton.vue";
import ErrorTemplate from "./others/errorTemplate.vue";
import TReadMore from "./others/tReadMore.vue";
import ScreenLocker from "./others/screenLocker.vue";
import TPopover from "./tPopover.vue";
import PageResultCount from "./others/pageResultCount.vue";
import SearchableColumn from "./others/searchableColumn.vue";
import SelectionColumn from "./others/selectionColumn.vue";
import TSkeleton from "./skeletons/index.vue";
import TPin from "./others/tPin.vue";
import TPageLoader from "./others/pageLoader.vue";
import TCodeScan from "./others/tCodeScan/index.vue";
import TCamera from "./others/tCamera/index.vue";
// End - Others

// Viewers
import TPdf from "./viewers/pdf/index.vue";
// End - Viewers

export default {
  TInnerLoading,
  TIcon,
  TFileIcon,
  TButton,
  TCollapse,
  TImage,
  TToolTip,
  TMenu,
  TPagination,
  TTabProgress,

  // Layout
  Layout,
  Header,
  Footer,
  PageContainer,
  Page,
  SideBar,
  // End - Layout

  // Card
  TCard,
  TCardHeader,
  TCardBody,
  TCardFooter,
  TCardTitle,
  TDialog,
  // End - Card

  // Forms
  TInput,
  TFieldWrapper,
  TTextArea,
  TCheckBox,
  TRadio,
  TList,
  TSelect,
  TImageSelect,
  TDate,
  TSimpleDate,
  TFileDrop,
  TFileInfo,
  TToggle,
  TColorPicker,
  TTextEditor,

  TFormWizard,
  TFormWizardTab,
  // End - Forms

  // Spinners
  TSpinnerOrbit,
  TSpinnerCircles,
  // End - Spinners

  // Utils
  SizeObserver,
  // End - Utils

  // TProgress
  TProgress,
  TProgressBar,
  TProgressCircle,
  // End - TProgress

  // TTimeline
  TTimeline,
  // End - TTimeline

  // Others
  TScrollUp,
  TGroup,
  TAddress,
  TSearcher,
  TSearchButton,
  TPopover,
  TReadMore,

  ThemeToggle,
  FocusHelper,
  ErrorTemplate,
  ScreenLocker,
  PageResultCount,
  SearchableColumn,
  SelectionColumn,
  TSkeleton,
  TPin,
  TPageLoader,
  TCodeScan,
  TCamera,
  // End - Others

  // Viewers
  TPdf,
  // End - Viewers
};
