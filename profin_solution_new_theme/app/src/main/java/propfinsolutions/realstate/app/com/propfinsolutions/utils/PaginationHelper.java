package propfinsolutions.realstate.app.com.propfinsolutions.utils;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
public class PaginationHelper {
    public static void setUpPagination(RecyclerView recyclerView,
                                       LinearLayoutManager layoutManager,
                                       boolean isLoading,
                                       PaginationListener paginationListener) {

        recyclerView.addOnScrollListener(new RecyclerView.OnScrollListener() {
            @Override
            public void onScrolled(@NonNull RecyclerView recyclerView, int dx, int dy) {
                super.onScrolled(recyclerView, dx, dy);
                if (dy > 0) {
                    int visibleItemCount = layoutManager.getChildCount();
                    int totalItemCount = layoutManager.getItemCount();
                    int pastVisibleItem = layoutManager.findFirstVisibleItemPosition();

                    if (isLoading) {
                        if ((visibleItemCount + pastVisibleItem) >= totalItemCount) {
                            paginationListener.onLoadMore();
                        }
                    }
                }
            }
        });
    }

    public interface PaginationListener {
        void onLoadMore();
    }
}
