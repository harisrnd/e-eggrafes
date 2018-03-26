import { ValidatorFn } from "@angular/forms";
import { AbstractControl } from "@angular/forms";

export const ORIENTATIONGROUP_INIT = "ORIENTATIONGROUP_INIT";
export const ORIENTATIONGROUP_RECEIVED = "ORIENTATIONGROUP_RECEIVED";
export const ORIENTATIONGROUP_SAVE = "ORIENTATIONGROUP_SAVE";
export const ORIENTATIONGROUP_SAVE_WITHIDS= "ORIENTATIONGROUP_SAVE_WITHIDS";

export const ELECTIVECOURSEFIELDS_INIT = "ELECTIVECOURSEFIELDS_INIT";
export const ELECTIVECOURSEFIELDS_RECEIVED = "ELECTIVECOURSEFIELDS_RECEIVED";
export const ELECTIVECOURSEFIELDS_SELECTED_SAVE = "ELECTIVECOURSEFIELDS_SELECTED_SAVE";
export const ELECTIVECOURSES_ORDER_SAVE = "ELECTIVECOURSES_ORDER_SAVE";
export const ELECTIVECOURSEFIELDS_SELECTED_SAVE_WITHIDS = "ELECTIVECOURSEFIELDS_SELECTED_SAVE_WITHIDS";


export const LANGCOURSEFIELDS_INIT = "LANGCOURSEFIELDS_INIT";
export const LANGCOURSEFIELDS_RECEIVED = "LANGCOURSEFIELDS_RECEIVED";
export const LANGCOURSEFIELDS_SELECTED_SAVE = "LANGCOURSEFIELDS_SELECTED_SAVE";
export const LANGCOURSES_ORDER_SAVE = "LANGCOURSES_ORDER_SAVE";

export const GELCLASSES_INIT = "GELCLASSES_INIT";
export const GELCLASSES_RECEIVED = "GELCLASSES_RECEIVED";
export const GELCLASSES_SAVE = "GELCLASSES_SAVE";
export const GELCLASSES_RESET = "GELCLASSES_RESET";

export const SCHOOLTYPE_SAVE = "SCHOOLTYPE_SAVE";
export const SCHOOLTYPE_INIT = "SCHOOLTYPE_INIT";
export const SCHOOLTYPE_RECEIVED = "SCHOOLTYPE_RECEIVED";
export const SCHOOLTYPE_RESET = "SCHOOLTYPE_RESET";

export const GELSTUDENTDATAFIELDS_SAVE = "GELSTUDENTDATAFIELDS_SAVE";
export const GELSTUDENTDATAFIELDS_INIT = "GELSTUDENTDATAFIELDS_INIT";

export const COURSEFIELDS_RECEIVED = "COURSEFIELDS_RECEIVED";
export const COURSEFIELDS_SELECTED_SAVE = "COURSEFIELDS_SELECTED_SAVE";

export const SECTORFIELDS_RECEIVED = "SECTORFIELDS_RECEIVED";
export const SECTORFIELDS_SELECTED_SAVE = "SECTORFIELDS_SELECTED_SAVE";
export const SECTORFIELDS_INIT = "SECTORFIELDS_INIT";
export const SECTORFIELDS_SELECTED_SAVE_WITHIDS = "SECTORFIELDS_SELECTED_SAVE_WITHIDS"

export const REGIONSCHOOLS_RECEIVED = "REGIONSCHOOLS_RECEIVED";
export const REGIONSCHOOLS_SELECTED_SAVE = "REGIONSCHOOLS_SELECTED_SAVE";
export const REGIONSCHOOLS_ORDER_SAVE = "REGIONSCHOOLS_ORDER_SAVE";
export const REGIONSCHOOLS_INIT = "REGIONSCHOOLS_INIT";
export const REGIONSCHOOLS_SELECTED_SAVE_WITHIDS = "REGIONSCHOOLS_SELECTED_SAVE_WITHIDS";

export const SECTORCOURSES_RECEIVED = "SECTORCOURSES_RECEIVED";
export const SECTORCOURSES_SELECTED_SAVE = "SECTORCOURSES_SELECTED_SAVE";
export const SECTORCOURSES_INIT = "SECTORCOURSES_INIT";

export const STUDENTDATAFIELDS_SAVE = "STUDENTDATAFIELDS_SAVE";
export const STUDENTDATAFIELDS_INIT = "STUDENTDATAFIELDS_INIT";

export const EPALCLASSES_SAVE = "EPALCLASSES_SAVE";
export const EPALCLASSES_INIT = "EPALCLASSES_INIT";

export const DATAMODE_SAVE = "DATAMODE_SAVE";
export const DATAMODE_INIT = "DATAMODE_INIT";

export const LOGININFO_INIT = "LOGININFO_INIT";
export const LOGININFO_SAVE = "LOGININFO_SAVE";
export const LOGININFO_RECEIVED = "LOGININFO_RECEIVED";

export const PROFILE_SAVE = "PROFILE_SAVE";

export const STATEMENTAGREE_SAVE = "STATEMENTAGREE_SAVE";

export const USERINFOS_RECEIVED = "USERINFOS_RECEIVED";
export const USERINFO_SELECTED_SAVE = "USERINFO_SELECTED_SAVE";

export const CRITERIA_RECEIVED = "CRITERIA_RECEIVED";
export const CRITERIA_SAVE = "CRITERIA_SAVE";
export const CRITERIA_INIT = "CRITERIA_INIT";

export const VALID_NAMES_PATTERN = "^[A-Za-zΑ-ΩΆΈΉΊΙΎΌΏα-ωάέήίΐύόώ -]*$";
export const VALID_UCASE_NAMES_PATTERN = "^[A-ZΑ-Ω -]*$";
export const VALID_ADDRESS_PATTERN = "^[0-9A-Za-zΑ-ΩΆΈΉΊΎΌΏα-ωάέήίύόώ\/. -]*$";
export const VALID_ADDRESSTK_PATTERN = "^[0-9]{1,5}$";
export const VALID_DIGITS_PATTERN = "^[0-9]*$";
export const VALID_TELEPHONE_PATTERN = "^2[0-9]{0,10}$";
export const VALID_YEAR_PATTERN = "^(19[6789][0-9]|20[0-1][0-9])$";
export const VALID_CAPACITY_PATTERN = "[0-9]*$";
export const VALID_EMAIL_PATTERN = "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}";
export const VALID_DATE_PATTERN = "([1-9]|0[1-9]|[12][0-9]|3[01])[- /.]([1-9]|0[1-9]|1[012])[- /.](19|20)[0-9][0-9]";

export const SCHOOL_ROLE = "director";
export const SCHOOLGEL_ROLE = "director_gel";
export const STUDENT_ROLE = "student";
export const PDE_ROLE = "pde";
export const DIDE_ROLE = "dide";
export const MINISTRY_ROLE = "supervisor";
export const FIRST_SCHOOL_YEAR = 1950;

export function maxValue(max: Number): ValidatorFn {
    return (control: AbstractControl): { [key: string]: any } => {
        const input = control.value,
            isValid = input > 99;
        if (isValid)
            return { "maxValue": { max } };
        else
            return null;
    };
}

export function minValue(min: Number): ValidatorFn {
    return (control: AbstractControl): { [key: string]: any } => {
        const input = control.value,
            isValid = input < 1;
        if (isValid)
            return { "minValue": { min } };
        else
            return null;
    };
}
