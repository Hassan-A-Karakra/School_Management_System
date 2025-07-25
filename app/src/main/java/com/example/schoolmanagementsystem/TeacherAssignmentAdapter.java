package com.example.schoolmanagementsystem;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.DiffUtil;
import androidx.recyclerview.widget.ListAdapter;
import androidx.recyclerview.widget.RecyclerView;
import com.google.android.material.button.MaterialButton;

public class TeacherAssignmentAdapter extends ListAdapter<Assignment, TeacherAssignmentAdapter.AssignmentViewHolder> {
    private final OnAssignmentClickListener listener;

    // Interface for listening to click events on list items
    public interface OnAssignmentClickListener {
        void onMenuClick(Assignment assignment);

    }

    public TeacherAssignmentAdapter(OnAssignmentClickListener listener) {
        super(new AssignmentDiffCallback());
        this.listener = listener;
    }

    @NonNull
    @Override
    public AssignmentViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_assignment, parent, false);
        return new AssignmentViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull AssignmentViewHolder holder, int position) {
        holder.bind(getItem(position));
    }

    // ViewHolder for handling individual assignment items
    class AssignmentViewHolder extends RecyclerView.ViewHolder {
        private final TextView assignmentTitle;
        private final TextView assignmentDescription;
        private final TextView assignmentSubject;
        private final TextView dueDate;
        private final ImageButton menuButton;
        private final MaterialButton submitButton;

        AssignmentViewHolder(View itemView) {

            super(itemView);

            assignmentTitle = itemView.findViewById(R.id.assignmentTitle);
            assignmentDescription = itemView.findViewById(R.id.assignmentDescription);
            assignmentSubject = itemView.findViewById(R.id.assignmentSubject);
            dueDate = itemView.findViewById(R.id.dueDate);
            menuButton = itemView.findViewById(R.id.menuButton);
            submitButton = itemView.findViewById(R.id.button_submit_assignment);

        }

        void bind(Assignment assignment) {
            assignmentTitle.setText(assignment.getTitle());
            assignmentDescription.setText(assignment.getDescription());
            assignmentSubject.setText("Subject: " + assignment.getSubject());
            dueDate.setText(assignment.getDueDate());

            menuButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (listener != null) {
                        listener.onMenuClick(assignment);
                    }
                }
            });

            submitButton.setVisibility(View.GONE);
        }
    }

    private static class AssignmentDiffCallback extends DiffUtil.ItemCallback<Assignment> {
        @Override
        public boolean areItemsTheSame(@NonNull Assignment oldItem, @NonNull Assignment newItem) {
            return oldItem.getId() == newItem.getId();
        }

        @Override
        public boolean areContentsTheSame(@NonNull Assignment oldItem, @NonNull Assignment newItem) {
            // Compare all contents of the assignment
            return oldItem.getTitle().equals(newItem.getTitle()) &&
                    oldItem.getDescription().equals(newItem.getDescription()) &&
                    oldItem.getDueDate().equals(newItem.getDueDate()) &&
                    oldItem.getSubject().equals(newItem.getSubject());
        }
    }
}