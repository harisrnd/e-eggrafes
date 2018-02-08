import { IElectiveCourseFieldRecord, IElectiveCourseFieldRecords } from "./electivecoursesfields.types";
import { electivecourseFieldsReducer } from "./electivecoursesfields.reducer";
import { deimmutifyElectiveCourseFields } from "./electivecoursesfields.transformers";

export {
    IElectiveCourseFieldRecord,
    IElectiveCourseFieldRecords,
    electivecourseFieldsReducer,
    deimmutifyElectiveCourseFields,
};
