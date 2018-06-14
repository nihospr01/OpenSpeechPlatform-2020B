#ifndef LOGGER_H__
#define LOGGER_H__

FILE *file_logger_init(const char *filename);

int file_logger_log_osp_data(FILE *fd, osp_user_data *data);

int file_logger_log_message(FILE *fd, char *message);

void file_logger_close(FILE *fd);

#endif /* LOGGER_H__ */
