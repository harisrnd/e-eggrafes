import { ILangCourseFieldRecord, ILangCourseFieldRecords } from "./langcoursesfields.types";
import { langcourseFieldsReducer } from "./langcoursesfields.reducer";
import { deimmutifyLangCourseFields } from "./langcoursesfields.transformers";

export {
    ILangCourseFieldRecord,
    ILangCourseFieldRecords,
    langcourseFieldsReducer,
    deimmutifyLangCourseFields,
};
