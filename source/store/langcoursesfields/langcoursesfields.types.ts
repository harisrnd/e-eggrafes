import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface ILangCourseField {
    id: number;
    name: string;
    selected: boolean;
    order_id: number;
}

export interface ILangCourseFieldRecord extends TypedRecord<ILangCourseFieldRecord>, ILangCourseField { };
export type ILangCourseFieldRecords = List<ILangCourseFieldRecord>;
