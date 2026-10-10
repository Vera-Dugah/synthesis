# The Saturday Shelf

A five-page site for a one-room library that lends books on Saturday.

A visitor can see where the shelf is, browse the sections, check which
books are already out, ask to borrow one, and work out a late fee.

## Pages

| Page | File | Purpose |
| --- | --- | --- |
| The room | index.html | Where the shelf is, and that it opens Saturday |
| The shelves | shelves.html | Sections of the room, with photos |
| Loan board | board.php | Titles that are out, and a return when the book comes back |
| Borrow | borrow.php | A form to ask for a book |
| Late slip | slip.html | A fee worked out in the browser |

## Open it

AMPPS has to be running, including MySQL. Borrow requests are stored in the `saturday_shelf` database. The home page will be:

http://localhost/synthesis/
