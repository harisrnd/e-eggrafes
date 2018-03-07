import { Routes } from "@angular/router";

import DirectorClassCapacity from "../components/director/director-classcapacity";
import DirectorView from "../components/director/director-view";
import DirectorButtons from "../components/director/director.buttons";
import Home from "../components/home";
import EduadminView from "../components/infoviews/eduadmin-view";
import MergeSchools from "../components/mergeschools/mergeschools";
import UndoMergeSchools from "../components/mergeschools/undomerge";
import SmallClassApprovement from "../components/mergeschools/smallclassapprovment";
import PerfectureView from "../components/infoviews/perfecture-view";
import Breadcrumbs from "../components/main/breadcrumbs";
import InformStudents from "../components/minister/minister-informstudents";
import MinisterReports from "../components/minister/minister-reports";
import MinisterSettings from "../components/minister/minister-settings";
import MinisterView from "../components/minister/minister-view";
import ReportAllStat from "../components/minister/report-all-stat";
import ReportGeneral from "../components/minister/report-general";
import ReportNoCapacity from "../components/minister/report-no-capacity";
import ReportUsers from "../components/minister/report-users";
import MinistryHome from "../components/ministry.home";
import SchoolHome from "../components/school.home";
import AfterSubmit from "../components/student-application-form/after.submit";
import StudentApplicationMain from "../components/student-application-form/application.form.main";
import ApplicationPreview from "../components/student-application-form/application.preview";
import ApplicationSubmit from "../components/student-application-form/application.submit";
import Disclaimer from "../components/student-application-form/disclaimer";
import EpalClassesSelect from "../components/student-application-form/epal.class.select";
import HelpDesk from "../components/student-application-form/help-desk";
import Info from "../components/student-application-form/info";
import LegalInfo from "../components/student-application-form/legalinfos";
import ParentForm from "../components/student-application-form/parent.form";
import RegionSchoolsSelect from "../components/student-application-form/region.schools.select";
import SchoolsOrderSelect from "../components/student-application-form/schools-order-select";
import SectorCoursesSelect from "../components/student-application-form/sector.courses.select";
import SectorFieldsSelect from "../components/student-application-form/sector.fields.select";
import SubmitedPreview from "../components/student-application-form/submited.aplication.preview";
import EduAdminAuthGuard from "../guards/eduadmin.auth.guard";
import MinistryAuthGuard from "../guards/ministry.auth.guard";
import DidepdeAuthGuard from "../guards/dideandpde.auth.guard";
import RegionEduAuthGuard from "../guards/regionedu.auth.guard";
import ReportsAuthGuard from "../guards/reports.auth.guard";
import SchoolAuthGuard from "../guards/school.auth.guard";
import SchoolCapacityLockedGuard from "../guards/school.capacity.locked.guard";
import SchoolStudentsLockedGuard from "../guards/school.students.locked.guard";
import StudentAuthGuard from "../guards/student.auth.guard";
import StudentLockGuard from "../guards/student.lock.guard";
import { CamelCasePipe } from "../pipes/camelcase";
import { RemoveSpaces } from "../pipes/removespaces";
import ReportMergedClasses from "../components/minister/report-merged-classes";
import ReportApplications from "../components/minister/report-applications";
import ReportUserApplications from "../components/minister/report-user-applications";
import ReportGelStudents from "../components/minister/report-gel-students";
import ElectiveCourseFieldsSelect from "../components/student-application-form/electivecourse.fields.select";
import OrientationGroup from "../components/student-application-form/orientation.group";
import LangCourseFieldsSelect from "../components/student-application-form/langcourse.fields.select";
import CoursesOrderSelect from "../components/student-application-form/courses.order.select";
import ClassSelection from "../components/student-application-form/class.selection";
import GelStudentApplicationMain from "../components/student-application-form/gelapplication.form.main";
import SchoolTypeSelection from "../components/student-application-form/schooltype.selection";
import GelApplicationSubmit from "../components/student-application-form/gelapplication.submit";
import SchoolTypeSelectionPde from "../components/infoviews/school-type-selection";




export const MainRoutes: Routes = [
    { path: "", component: Home },
    { path: "info", component: Info, canActivate: [StudentAuthGuard] },
    { path: "logout", component: Home },
    { path: "school", component: SchoolHome },
    { path: "school/logout", component: SchoolHome },
    { path: "ministry", component: MinistryHome },
    { path: "ministry/logout", component: MinistryHome },
    { path: "parent-form", component: ParentForm, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "student-application-form-main", component: StudentApplicationMain, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "intro-statement", component: Disclaimer, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "legal-info", component: LegalInfo },
    { path: "epal-class-select", component: EpalClassesSelect, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "sector-fields-select", component: SectorFieldsSelect, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "region-schools-select", component: RegionSchoolsSelect, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "sectorcourses-fields-select", component: SectorCoursesSelect, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "application-preview", component: ApplicationPreview, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "schools-order-select", component: SchoolsOrderSelect, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "application-submit", component: ApplicationSubmit, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "submited-preview", component: SubmitedPreview, canActivate: [StudentAuthGuard] },
    { path: "post-submit", component: AfterSubmit, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "school/director-view", component: DirectorView, canActivate: [SchoolAuthGuard, SchoolStudentsLockedGuard] },
    { path: "school/director-buttons", component: DirectorButtons, canActivate: [SchoolAuthGuard] },
    { path: "school/director-classcapacity", component: DirectorClassCapacity, canActivate: [SchoolAuthGuard, SchoolCapacityLockedGuard] },
    { path: "ministry/minister-view", component: MinisterView, canActivate: [MinistryAuthGuard] },
    { path: "school/school-type-selection", component: SchoolTypeSelectionPde, canActivate: [RegionEduAuthGuard] },
    { path: "ministry/minister-reports", component: MinisterReports, canActivate: [ReportsAuthGuard] },
    { path: "ministry/report-all-stat/:reportId", component: ReportAllStat, canActivate: [ReportsAuthGuard] },
    { path: "ministry/report-general", component: ReportGeneral, canActivate: [MinistryAuthGuard] },
    { path: "ministry/report-users/:reportId", component: ReportUsers, canActivate: [MinistryAuthGuard] },
    { path: "ministry/report-no-capacity/:reportId", component: ReportNoCapacity, canActivate: [MinistryAuthGuard] },
    { path: "ministry/minister-informstudents", component: InformStudents, canActivate: [MinistryAuthGuard] },
    { path: "ministry/minister-settings", component: MinisterSettings, canActivate: [MinistryAuthGuard] },
    { path: "school/perfecture-view", component: PerfectureView, canActivate: [RegionEduAuthGuard] },
    { path: "school/eduadmin-view", component: EduadminView, canActivate: [EduAdminAuthGuard] },
    { path: "school/mergeschools", component: MergeSchools, canActivate: [DidepdeAuthGuard] },
    { path: "school/undomerge", component: UndoMergeSchools, canActivate: [DidepdeAuthGuard] },
    { path: "school/smallclassapprovement", component: SmallClassApprovement, canActivate: [RegionEduAuthGuard] },
    { path: "help-desk", component: HelpDesk, canActivate: [StudentAuthGuard] },
    { path: "ministry/report-merged-classes", component: ReportMergedClasses, canActivate: [ReportsAuthGuard] },
    { path: "ministry/report-applications", component: ReportApplications, canActivate: [ReportsAuthGuard] },
    { path: "ministry/report-user-applications", component: ReportUserApplications, canActivate: [ReportsAuthGuard] },
    { path: "ministry/report-gel-students", component: ReportGelStudents, canActivate: [ReportsAuthGuard] },
    { path: "orientation-group-select", component: OrientationGroup, canActivate: [StudentAuthGuard, StudentLockGuard]},
    { path: "electivecourse-fields-select", component: ElectiveCourseFieldsSelect, canActivate: [StudentAuthGuard, StudentLockGuard]},
    { path: "langcourse-fields-select", component: LangCourseFieldsSelect, canActivate: [StudentAuthGuard, StudentLockGuard]},
    { path: "course-order-select", component: CoursesOrderSelect, canActivate: [StudentAuthGuard, StudentLockGuard]},
    { path: "gel-class-select", component: ClassSelection, canActivate: [StudentAuthGuard, StudentLockGuard]},
    { path: "gelstudent-application-form-main", component: GelStudentApplicationMain, canActivate: [StudentAuthGuard, StudentLockGuard] },
    { path: "school-type-select", component: SchoolTypeSelection, canActivate: [StudentAuthGuard, StudentLockGuard]},
    { path: "gel-application-submit", component: GelApplicationSubmit, canActivate: [StudentAuthGuard, StudentLockGuard] },

];

export const MainDeclarations = [
    CamelCasePipe,
    RemoveSpaces,
    Home,
    SchoolHome,
    MinistryHome,
    Disclaimer,
    EpalClassesSelect,
    SectorFieldsSelect,
    RegionSchoolsSelect,
    SectorCoursesSelect,
    ParentForm,
    Info,
    StudentApplicationMain,
    ApplicationPreview,
    SchoolsOrderSelect,
    ApplicationSubmit,
    SubmitedPreview,
    AfterSubmit,
    DirectorView,
    DirectorClassCapacity,
    MinisterView,
    MinisterReports,
    ReportAllStat,
    ReportGeneral,
    ReportUsers,
    ReportNoCapacity,
    InformStudents,
    MinisterSettings,
    PerfectureView,
    Breadcrumbs,
    DirectorButtons,
    EduadminView,
    HelpDesk,
    LegalInfo,
    MergeSchools,
    UndoMergeSchools,
    SmallClassApprovement,
    ReportMergedClasses,
    ReportApplications,
    ReportUserApplications,
    ReportGelStudents,
    OrientationGroup,
    ElectiveCourseFieldsSelect,
    LangCourseFieldsSelect,
    CoursesOrderSelect,
    ClassSelection,
    GelStudentApplicationMain,
    SchoolTypeSelection,
    GelApplicationSubmit,
    SchoolTypeSelectionPde
];
