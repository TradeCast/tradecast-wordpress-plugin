/**
 * Components
 */
export type TableColumnType = {
  className?: string | string[];
  name: string;
  property: string;
  title?: string | null;
  type?: string;
};

export type TableRowType<T> = T & {
  className?: string | string[];
};

export type MessageType = {
  uuid?: string;
  className: string;
  message: string;
};

/**
 * State
 */
export type MessageState = {
  flashMessages?: MessageType[];
  toastMessages?: MessageType[];
};

export type RootState = {
  messages: MessageState;
};
