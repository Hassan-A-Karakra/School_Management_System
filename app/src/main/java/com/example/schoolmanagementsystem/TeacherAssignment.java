package com.example.schoolmanagementsystem;

public class TeacherAssignment {

        private int id;
        private String title;
        private String description;
        private String dueDate;

        public TeacherAssignment(int id, String title, String description, String dueDate) {
            this.id = id;
            this.title = title;
            this.description = description;
            this.dueDate = dueDate;
        }

        public int getId() {
            return id;
        }

        public String getTitle() {
            return title;
        }

        public String getDescription() {
            return description;
        }

        public String getDueDate() {
            return dueDate;
        }

        public void setId(int id) {
            this.id = id;
        }

        public void setTitle(String title) {
            this.title = title;
        }

        public void setDescription(String description) {
            this.description = description;
        }

        public void setDueDate(String dueDate) {
            this.dueDate = dueDate;
        }
    }

