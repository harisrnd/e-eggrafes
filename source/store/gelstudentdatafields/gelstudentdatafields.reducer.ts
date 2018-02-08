import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { GELSTUDENTDATAFIELDS_INIT, GELSTUDENTDATAFIELDS_SAVE } from "../../constants";
import { GELSTUDENT_DATA_FIELDS_INITIAL_STATE } from "./gelstudentdatafields.initial-state";
import { IGelStudentDataField, IGelStudentDataFieldRecord, IGelStudentDataFieldRecords } from "./gelstudentdatafields.types";

export function gelstudentDataFieldsReducer(state: IGelStudentDataFieldRecords = GELSTUDENT_DATA_FIELDS_INITIAL_STATE, action): IGelStudentDataFieldRecords {
    switch (action.type) {
        case GELSTUDENTDATAFIELDS_SAVE:
            let gelstudentDataFields = Array<IGelStudentDataFieldRecord>();

            action.payload.gelstudentDataFields.forEach(gelstudentDataField => {

                let transformedDate = "";
                if (gelstudentDataField.studentbirthdate && gelstudentDataField.studentbirthdate.date) {
                    transformedDate = gelstudentDataField.studentbirthdate.date.year + "-";
                    transformedDate += gelstudentDataField.studentbirthdate.date.month < 10 ? "0" + gelstudentDataField.studentbirthdate.date.month + "-" : gelstudentDataField.studentbirthdate.date.month + "-";
                    transformedDate += gelstudentDataField.studentbirthdate.date.day < 10 ? "0" + gelstudentDataField.studentbirthdate.date.day : gelstudentDataField.studentbirthdate.date.day;
                }

                gelstudentDataField.studentbirthdate = transformedDate;
                gelstudentDataFields.push(recordify<IGelStudentDataField, IGelStudentDataFieldRecord>(gelstudentDataField));
            });

            return List(gelstudentDataFields);
        case GELSTUDENTDATAFIELDS_INIT:
            return GELSTUDENT_DATA_FIELDS_INITIAL_STATE;
        default: return state;
    }
};
