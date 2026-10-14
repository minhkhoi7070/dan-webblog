# BlogMNM — Activity Diagrams

This document contains UML Activity Diagrams depicting the end-to-end decision logic, branching conditions, and state transitions for the four primary workflows in BlogMNM.

---

## 1. Activity Diagram: Author Publishing & Editorial Revision Workflow

```mermaid
flowchart TD
    Start([Author clicks 'New Post']) --> FillForm[Fill title, body, category, tags, thumbnail]
    FillForm --> SaveDraft[Click 'Save Draft']
    SaveDraft --> ValidCheck{Validation Passes?}
    
    ValidCheck -- No --> ShowErrors[Display form errors on fields] --> FillForm
    ValidCheck -- Yes --> StoreDraft[Store post with status = 'draft', views = 0]
    StoreDraft --> UploadCheck{Thumbnail uploaded?}
    UploadCheck -- Yes --> SaveThumb[Store thumbnail on public disk]
    UploadCheck -- No --> ReadyInCMS[Post saved in Author CMS]
    SaveThumb --> ReadyInCMS
    
    ReadyInCMS --> AuthorChoice{Author Action?}
    AuthorChoice -- Edit Content --> EditPost[Modify text / Replace thumbnail]
    EditPost --> ValidCheck
    AuthorChoice -- Delete --> ConfirmDel{Confirm Delete?}
    ConfirmDel -- Yes --> DeletePost[Delete record & delete thumbnail from disk] --> EndDraft([Post Deleted])
    ConfirmDel -- No --> ReadyInCMS
    AuthorChoice -- Submit for Review --> SubmitPost[Click 'Submit for Review']
    
    SubmitPost --> PolicyCheck{User active & Owner?}
    PolicyCheck -- Locked / Not Owner --> BlockSubmit[403 Forbidden]
    PolicyCheck -- Authorized --> UpdateStatus[Update status = 'pending', clear rejection_reason]
    UpdateStatus --> Queued([Post in Admin Review Queue])
```

---

## 2. Activity Diagram: Administrator Post Moderation Workflow

```mermaid
flowchart TD
    StartAdmin([Admin visits /admin/posts]) --> SelectPost[Select pending post to review]
    SelectPost --> InspectPost[Read content, tags, author, thumbnail]
    InspectPost --> Decision{Editorial Decision?}
    
    Decision -- Approve --> ClickApprove[Click 'Approve']
    ClickApprove --> ProcessApproval[Set status = 'published'<br/>Set reviewed_by = admin.id<br/>Set reviewed_at = now()<br/>Set published_at = now()]
    ProcessApproval --> LiveFeed[Post immediately visible in public home feed]
    LiveFeed --> EndApprove([Published])
    
    Decision -- Reject --> OpenModal[Open Rejection Feedback Modal]
    OpenModal --> EnterReason[Enter constructive feedback reason]
    EnterReason --> ClickReject[Click 'Reject Post']
    ClickReject --> ProcessRejection[Set status = 'rejected'<br/>Set reviewed_by = admin.id<br/>Set reviewed_at = now()<br/>Save rejection_reason]
    ProcessRejection --> NotifyAuthor[Rejection visible to Author in CMS]
    NotifyAuthor --> AuthorRevise[Author revises content & resubmits]
    AuthorRevise --> SelectPost
    
    Decision -- Delete --> ClickDelete[Click 'Delete Post']
    ClickDelete --> ProcessDelete[Delete post & unlink thumbnail]
    ProcessDelete --> EndDelete([Post Permanently Removed])
```

---

## 3. Activity Diagram: Viewer Comment & Moderation Workflow

```mermaid
flowchart TD
    StartComment([Viewer views published article]) --> WriteComment[Write comment text]
    WriteComment --> AuthCheck{Is Viewer Authenticated?}
    
    AuthCheck -- No --> PromptLogin[Display Login / Register Prompt]
    AuthCheck -- Yes --> LockCheck{Is User Locked?}
    
    LockCheck -- Yes --> Forbidden403[403 Forbidden: Account is locked]
    LockCheck -- No --> CheckParent{Is Reply to Comment?}
    
    CheckParent -- Yes --> CrossPostCheck{Parent belongs to this post?}
    CrossPostCheck -- No --> CrossPostError[422 Error: Parent comment belongs to another post]
    CrossPostCheck -- Yes --> PersistReply[Save comment with parent_id & status = 'approved']
    
    CheckParent -- No --> PersistRoot[Save root comment with status = 'approved']
    
    PersistReply --> UpdateDOM[Inject dynamically into comment tree via AJAX]
    PersistRoot --> UpdateDOM
    UpdateDOM --> DoneComment([Comment Visible to Readers])
    
    DoneComment --> AdminReview{Admin Flags Comment?}
    AdminReview -- Mark Spam --> SetSpam[Set status = 'spam'] --> HidePublic[Hidden from public view]
    AdminReview -- Delete --> DeleteComment[Delete comment from DB] --> RemoveDOM[Removed completely]
    AdminReview -- None --> RemainActive([Remains Active])
```

---

## 4. Activity Diagram: User Account Locking & Governance

```mermaid
flowchart TD
    StartGov([Admin visits /admin/users]) --> SearchUser[Locate abusive user]
    SearchUser --> ClickLock[Click 'Lock User']
    ClickLock --> VerifyAdmin{Target role == 'admin'?}
    
    VerifyAdmin -- Yes (SEC-05) --> BlockLockout[Reject Action: Administrators cannot be locked]
    BlockLockout --> FlashError[Show warning flash banner] --> DoneAbort([Action Blocked])
    
    VerifyAdmin -- No --> SetLocked[Update is_locked = true in database]
    SetLocked --> FlashSuccess[Show 'User account locked' notice]
    
    SetLocked -. Next User Request .-> LockedUserAttempt[Locked user tries any action]
    LockedUserAttempt --> ActionType{Action Type?}
    
    ActionType -- Login Attempt --> EjectSession[Auth::logout, Invalidate Session, 422 Error]
    ActionType -- Write Post / Edit --> PolicyBlock[PostPolicy before() returns false -> 403 Forbidden]
    ActionType -- Post Comment --> ReqBlock[StoreCommentRequest returns false -> 403 Forbidden]
    ActionType -- Like / Favorite / Follow --> CtrlBlock[abort_if is_locked -> 403 Forbidden]
    ActionType -- Access Protected Route --> MWBlock[RoleMiddleware aborts 403 Forbidden]
```
