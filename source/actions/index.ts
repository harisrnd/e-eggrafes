import { SectorFieldsActions } from "./sectorfields.actions";
import { RegionSchoolsActions } from "./regionschools.actions";
import { SectorCoursesActions } from "./sectorcourses.actions";
import { StudentDataFieldsActions } from "./studentdatafields.actions";
import { EpalClassesActions } from "./epalclass.actions";
import { DataModeActions } from "./datamode.actions";
import { LoginInfoActions } from "./logininfo.actions";

import { OrientationGroupActions } from "./orientationgroup.action";
import { ElectiveCourseFieldsActions } from "./electivecoursesfields.actions";
import { LangCourseFieldsActions } from "./langcoursesfields.actions";
import { GelClassesActions } from "./gelclasses.actions";
import { GelStudentDataFieldsActions } from "./gelstudentdatafields.actions";
import { SchoolTypeActions } from "./schooltype.actions";


const ACTION_PROVIDERS = [
    SectorFieldsActions,
    RegionSchoolsActions,
    SectorCoursesActions,
    StudentDataFieldsActions,
    EpalClassesActions,
    DataModeActions,
    LoginInfoActions,

    OrientationGroupActions,
    ElectiveCourseFieldsActions,
    LangCourseFieldsActions,
    GelClassesActions,
    GelStudentDataFieldsActions,
    SchoolTypeActions,
];

export {
    SectorFieldsActions,
    RegionSchoolsActions,
    SectorCoursesActions,
    StudentDataFieldsActions,
    EpalClassesActions,
    DataModeActions,
    LoginInfoActions,

    OrientationGroupActions,
    ElectiveCourseFieldsActions,
    LangCourseFieldsActions,
    GelClassesActions,
    GelStudentDataFieldsActions,
    SchoolTypeActions,

    ACTION_PROVIDERS,
};
