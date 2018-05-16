import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { LOGININFO_INIT, LOGININFO_SAVE, PROFILE_SAVE, STATEMENTAGREE_SAVE } from "../../constants";
import { LOGININFO_INITIAL_STATE } from "./logininfo.initial-state";
import { ILoginInfoObj, ILoginInfoRecord, ILoginInfoRecords } from "./logininfo.types";

export function loginInfoReducer(state: ILoginInfoRecords = LOGININFO_INITIAL_STATE, action): ILoginInfoRecords {
    switch (action.type) {
        case LOGININFO_SAVE:

            let loginInfoArray = Array<ILoginInfoRecord>();

            action.payload.loginInfos.forEach(loginInfo => {
                loginInfoArray.push(recordify<ILoginInfoObj, ILoginInfoRecord>({
                    auth_token: loginInfo.auth_token,
                    auth_role: loginInfo.auth_role,
                    cu_name: loginInfo.cu_name,
                    cu_surname: loginInfo.cu_surname,
                    cu_fathername: loginInfo.cu_fathername,
                    cu_mothername: loginInfo.cu_mothername,
                    cu_email: loginInfo.cu_email,
                    minedu_username: loginInfo.minedu_username,
                    minedu_userpassword: loginInfo.minedu_userpassword,
                    lock_capacity: loginInfo.lock_capacity,
                    lock_students_epal: loginInfo.lock_students_epal,
                    lock_students_gel: loginInfo.lock_students_gel,
                    lock_application_epal: loginInfo.lock_application_epal,
                    lock_application_gel: loginInfo.lock_application_gel,
                    disclaimer_checked: loginInfo.disclaimer_checked,
                    ws_ident: loginInfo.ws_ident,
                    guardian_ident: loginInfo.guardian_ident
                  
                }));
        });

        return List(loginInfoArray);

        case PROFILE_SAVE:
            return state.withMutations(function(list) {
                list.setIn([0, "cu_name"], action.payload.profile.userName);
                list.setIn([0, "cu_surname"], action.payload.profile.userSurname);
                list.setIn([0, "cu_fathername"], action.payload.profile.userFathername);
                list.setIn([0, "cu_mothername"], action.payload.profile.userMothername);
            });

        case STATEMENTAGREE_SAVE:
            return state.withMutations(function(list) {
                list.setIn([0, "disclaimer_checked"], action.payload.disclaimer_checked);
            });

        case LOGININFO_INIT:
            return LOGININFO_INITIAL_STATE;
        default:
            return state;
    }
};
